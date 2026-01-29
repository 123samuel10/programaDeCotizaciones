{{-- resources/views/cliente/cotizaciones/partials/card.blade.php --}}

@php
    use App\Models\Venta;

    $estado = $c->estado ?? 'pendiente';

    $badge = match($estado) {
        'aceptada'  => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
        'rechazada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
        default     => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
    };

    $estadoLabel = match($estado) {
        'aceptada'  => 'Aceptada',
        'rechazada' => 'Rechazada',
        default     => 'Pendiente',
    };

    $created = optional($c->created_at)->format('d/m/Y H:i');

    // ✅ Venta asociada
    $venta = Venta::where('cotizacion_id', $c->id)->first();
    $ventaEstado = $venta->estado_venta ?? null;

    $ventaEstadoLabel = match($ventaEstado) {
        'pagada'    => 'Pagada',
        'cancelada' => 'Cancelada',
        default     => 'Pendiente de pago',
    };

    // Preview: máximo 2 productos
    $items = $c->items ?? collect();
    $preview = $items->take(2);
    $faltan = max(0, $items->count() - $preview->count());

    // ✅ Auto-open modal si viene de seleccionar método / subir comprobante
    $abrirEste = (string)request('open') === (string)$c->id
              || (string)session('open_detalle') === (string)$c->id;
@endphp

<div
    class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm"
    x-data="{ openDetalle: @js($abrirEste) }"
>
    {{-- HEADER compact --}}
    <div class="p-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            {{-- Izquierda --}}
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                        Cotización #{{ $c->id }}
                    </h3>

                    <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 {{ $badge }}">
                        {{ $estadoLabel }}
                    </span>

                    @if($estado === 'aceptada' && $venta)
                        <span class="text-xs font-bold px-3 py-1 rounded-full ring-1
                            bg-blue-50 text-blue-800 ring-blue-100 dark:bg-blue-900/20 dark:text-blue-200 dark:ring-blue-900">
                            Venta #{{ $venta->id }} · {{ $ventaEstadoLabel }}
                        </span>
                    @endif
                </div>

                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Creada: {{ $created }}
                </div>

                {{-- Preview productos --}}
                <div class="mt-3">
                    <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Incluye</div>

                    <div class="mt-1 flex flex-col gap-1 text-sm text-gray-700 dark:text-gray-200">
                        @forelse($preview as $it)
                            @php $p = $it->producto; @endphp
                            <div class="flex items-center justify-between gap-3">
                                <div class="truncate">
                                    <span class="font-semibold">{{ $p->nombre_producto ?? 'Producto' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        · {{ $p->marca ?? '—' }} {{ $p->modelo ? '· '.$p->modelo : '' }}
                                    </span>
                                </div>
                                <div class="shrink-0 text-xs text-gray-500 dark:text-gray-400">
                                    x{{ (int)$it->cantidad }}
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500 dark:text-gray-400">Sin productos.</div>
                        @endforelse

                        @if($faltan > 0)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                + {{ $faltan }} producto(s) más…
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Derecha --}}
            <div class="shrink-0 lg:text-right">
                <div class="text-xs text-gray-500 dark:text-gray-400">Total a pagar</div>
                <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                    ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
                </div>

                <div class="mt-3 flex flex-col sm:flex-row gap-2 sm:justify-end">
                    <button
                        type="button"
                        x-on:click="openDetalle = true"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                               bg-gray-900 text-white font-extrabold hover:bg-gray-800
                               dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                               shadow-sm hover:shadow transition"
                    >
                        Ver detalle
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>

                    @if($estado === 'pendiente')
                        <button
                            type="button"
                            onclick="openRespuestaModal('rechazar','{{ route('cliente.cotizaciones.rechazar', $c->id) }}')"
                            class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold"
                        >
                            Rechazar
                        </button>

                        <button
                            type="button"
                            onclick="openRespuestaModal('aceptar','{{ route('cliente.cotizaciones.aceptar', $c->id) }}')"
                            class="px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-extrabold"
                        >
                            Aceptar
                        </button>
                    @endif
                </div>

                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    @if($estado === 'pendiente')
                        Acción requerida: revisa y responde.
                    @elseif($estado === 'aceptada')
                        Lista para pago.
                    @else
                        Cotización rechazada.
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETALLE --}}
    @include('cliente.cotizaciones.partials.detalle-modal', ['c' => $c, 'venta' => $venta])
</div>
