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
    $respond = $c->respondida_en
        ? \Carbon\Carbon::parse($c->respondida_en)->format('d/m/Y H:i')
        : null;

    // ✅ Venta asociada
    $venta = Venta::where('cotizacion_id', $c->id)->first();

    $metodo     = $venta->metodo_pago ?? null; // efectivo | transferencia
    $ventaEstado = $venta->estado_venta ?? null;

    $compPath   = $venta->comprobante_path ?? null;
    $compEstado = $venta->comprobante_estado ?? null; // pendiente_revision | aceptado | rechazado
    $compNota   = $venta->comprobante_nota_admin ?? null;

    $badgeComp = match($compEstado) {
        'aceptado'  => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
        'rechazado' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
        default     => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
    };

    $ventaEstadoLabel = match($ventaEstado) {
        'pagada' => 'Pagada',
        'cancelada' => 'Cancelada',
        default => 'Pendiente de pago',
    };

    $compEstadoLabel = match($compEstado) {
        'aceptado' => 'Aprobado',
        'rechazado' => 'Rechazado',
        'pendiente_revision' => 'Pendiente de revisión',
        default => null,
    };

    // Datos cuenta desde .env
    $pagoBanco   = env('PAGO_BANCO', 'Banco');
    $pagoCuenta  = env('PAGO_CUENTA', '0000000000');
    $pagoTitular = env('PAGO_TITULAR', 'Heral Enterprises');
@endphp

<div
    class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm"
    x-data="{ open:false }"
>
    {{-- HEADER --}}
    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

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
                    @if($respond)
                        · Respondida: {{ $respond }}
                    @endif
                </div>

                {{-- Mensaje guía (cliente-friendly) --}}
                <div class="mt-3">
                    @if($estado === 'pendiente')
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Revisa el detalle y, si estás de acuerdo, puedes <b>aceptar</b> o <b>rechazar</b>.
                        </p>
                    @elseif($estado === 'aceptada')
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            ✅ Aceptaste esta cotización. A continuación verás los pasos para realizar el pago.
                        </p>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            ❌ Rechazaste esta cotización. Si necesitas ajustes, puedes contactarnos.
                        </p>
                    @endif
                </div>
            </div>

            <div class="shrink-0 text-right">
                <div class="text-xs text-gray-500 dark:text-gray-400">Total a pagar</div>
                <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                    ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
                </div>

                <button
                    type="button"
                    x-on:click="open = !open"
                    class="mt-3 inline-flex items-center justify-center gapgap-2 px-4 py-2 rounded-xl
                           bg-gray-900 text-white font-extrabold hover:bg-gray-800
                           dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                >
                    <span x-text="open ? 'Ocultar detalle' : 'Ver detalle'"></span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         :class="open ? 'rotate-180 transition' : 'transition'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- DETALLE --}}
    <div x-show="open" x-collapse>
        <div class="p-6 space-y-6">

            {{-- RESUMEN (rápido para cliente) --}}
            <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                            Resumen
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $c->items->count() }} producto(s) en la cotización.
                        </div>
                    </div>

                    <div class="flex gap-2 flex-wrap">
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            Total: <span class="font-extrabold">${{ number_format((float)$c->total_venta, 2, ',', '.') }}</span>
                        </span>

                        @if($estado === 'pendiente')
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-yellow-50 text-yellow-800 ring-1 ring-yellow-100
                                         dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20">
                                Acción requerida
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="space-y-4">
                @foreach($c->items as $item)
                    @php
                        $p = $item->producto;

                        $baseVentaLinea = (float)$item->precio_base_venta * (int)$item->cantidad;
                        $adicVentaLinea = (float)$item->opciones->sum('subtotal_venta');
                        $totalLinea     = $baseVentaLinea + $adicVentaLinea;

                        // ✅ Ficha (campos de tu migración)
                        $repisas = $p->repisas_iluminadas ?? null;
                        $refrigerante = $p->refrigerante ?? null;
                        $dim = trim(($p->longitud ?? '').'×'.($p->profundidad ?? '').'×'.($p->altura ?? ''), '×');
                    @endphp

                    <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-5 bg-white dark:bg-gray-800">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-base font-extrabold text-gray-900 dark:text-gray-100">
                                            {{ $p->nombre_producto ?? 'Producto' }}
                                        </h4>

                                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-700
                                                     dark:bg-gray-700/60 dark:text-gray-200">
                                            {{ ($p->marca ?? '—') }} · {{ ($p->modelo ?? '—') }}
                                        </span>
                                    </div>

                                    {{-- mini ficha técnica (cliente-friendly) --}}
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-600 dark:text-gray-300">
                                        @if(!is_null($repisas))
                                            <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                                                Repisas iluminadas: <b>{{ $repisas }}</b>
                                            </span>
                                        @endif

                                        @if($refrigerante)
                                            <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                                                Refrigerante: <b>{{ $refrigerante }}</b>
                                            </span>
                                        @endif

                                        @if($dim !== '')
                                            <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                                                Dimensiones: <b>{{ $dim }}</b>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                        Cantidad: <b>{{ (int)$item->cantidad }}</b>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Total del producto</div>
                                    <div class="text-xl font-extrabold text-gray-900 dark:text-gray-100">
                                        ${{ number_format($totalLinea, 2, ',', '.') }}
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                        Base: ${{ number_format($baseVentaLinea, 2, ',', '.') }}
                                        · Adiciones: ${{ number_format($adicVentaLinea, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Adiciones --}}
                            <div class="mt-4">
                                <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100 mb-2">
                                    Adiciones incluidas
                                </div>

                                @if($item->opciones->isEmpty())
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Este producto no tiene adiciones.
                                    </p>
                                @else
                                    <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                                <tr>
                                                    <th class="py-2 px-4 text-left">Adición</th>
                                                    <th class="py-2 px-4 text-right">Cantidad</th>
                                                    <th class="py-2 px-4 text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($item->opciones as $op)
                                                    <tr class="border-t border-gray-100 dark:border-gray-700">
                                                        <td class="py-2 px-4 font-semibold text-gray-900 dark:text-gray-100">
                                                            {{ $op->opcion->nombre ?? '—' }}
                                                        </td>
                                                        <td class="py-2 px-4 text-right">
                                                            {{ (int)$op->cantidad }}
                                                        </td>
                                                        <td class="py-2 px-4 text-right font-extrabold">
                                                            ${{ number_format((float)$op->subtotal_venta, 2, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TOTAL GENERAL --}}
            <div class="p-5 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="text-sm font-extrabold text-blue-900 dark:text-blue-200">
                            Total general
                        </div>
                        <div class="text-xs text-blue-800/70 dark:text-blue-200/70">
                            Valor final a pagar (incluye adiciones).
                        </div>
                    </div>

                    <div class="text-3xl font-extrabold text-blue-900 dark:text-blue-100">
                        ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- ✅ PAGO (solo si está aceptada) --}}
            @if($estado === 'aceptada' && $venta)
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div>
                                <h4 class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                    Pago
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Venta #{{ $venta->id }} · {{ $ventaEstadoLabel }}
                                </p>
                            </div>

                            @if($compEstadoLabel)
                                <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 {{ $badgeComp }}">
                                    Comprobante: {{ $compEstadoLabel }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 rounded-2xl bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700 p-4">
                            <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                Paso 1 · Elige tu método de pago
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                Selecciona la opción que prefieras y luego presiona <b>Guardar método</b>.
                            </p>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Seleccionar método --}}
                        <form method="POST" action="{{ route('cliente.ventas.metodo', $venta->id) }}" class="space-y-3">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                    <input class="mt-1" type="radio" name="metodo_pago" value="efectivo" {{ $metodo === 'efectivo' ? 'checked' : '' }}>
                                    <div>
                                        <div class="font-extrabold text-gray-900 dark:text-gray-100">Efectivo</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Pagas en efectivo al recibir / acordar la entrega.
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                    <input class="mt-1" type="radio" name="metodo_pago" value="transferencia" {{ $metodo === 'transferencia' ? 'checked' : '' }}>
                                    <div>
                                        <div class="font-extrabold text-gray-900 dark:text-gray-100">Transferencia</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Transfiere y sube tu comprobante para revisión.
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex justify-end">
                                <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                    Guardar método
                                </button>
                            </div>
                        </form>

                        {{-- Transferencia: datos + comprobante --}}
                        @if($metodo === 'transferencia')
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-5 space-y-3">
                                <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                    Paso 2 · Realiza la transferencia
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 p-3">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Banco</div>
                                        <div class="font-extrabold text-gray-900 dark:text-gray-100">{{ $pagoBanco }}</div>
                                    </div>

                                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 p-3">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Cuenta</div>
                                        <div class="font-extrabold text-gray-900 dark:text-gray-100">{{ $pagoCuenta }}</div>
                                    </div>

                                    <div class="rounded-xl bg-white/80 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 p-3">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Titular</div>
                                        <div class="font-extrabold text-gray-900 dark:text-gray-100">{{ $pagoTitular }}</div>
                                    </div>
                                </div>

                                <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100 mt-3">
                                    Paso 3 · Sube tu comprobante
                                </div>

                                @if($compPath)
                                    <div class="text-sm text-gray-700 dark:text-gray-200">
                                        <span class="font-bold">Comprobante actual:</span>
                                        <a class="text-blue-600 hover:underline font-extrabold"
                                           href="{{ asset('storage/'.$compPath) }}"
                                           target="_blank">
                                            Ver archivo
                                        </a>
                                    </div>
                                @endif

                                @if($compEstado === 'rechazado' && $compNota)
                                    <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20 text-red-700 dark:text-red-200">
                                        <div class="font-extrabold">Tu comprobante fue rechazado</div>
                                        <div class="text-sm mt-1">{{ $compNota }}</div>
                                        <div class="text-sm mt-2 font-semibold">Puedes volver a subir uno nuevo.</div>
                                    </div>
                                @endif

                                @if($ventaEstado !== 'pagada')
                                    <form method="POST"
                                          action="{{ route('cliente.ventas.comprobante', $venta->id) }}"
                                          enctype="multipart/form-data"
                                          class="grid grid-cols-1 sm:grid-cols-6 gap-3 items-end">
                                        @csrf

                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                                Referencia (opcional)
                                            </label>
                                            <input type="text" name="referencia_pago"
                                                   value="{{ old('referencia_pago', $venta->referencia_pago ?? '') }}"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                                Comprobante (jpg/png/pdf)
                                            </label>
                                            <input type="file" name="comprobante"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>

                                        <div class="sm:col-span-1">
                                            <button class="w-full px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-extrabold">
                                                Enviar
                                            </button>
                                        </div>
                                    </form>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Al enviar tu comprobante, quedará <b>pendiente de revisión</b>.
                                    </p>
                                @else
                                    <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-500/10 border border-green-100 dark:border-green-500/20 text-green-800 dark:text-green-200">
                                        <div class="font-extrabold">Pago aprobado</div>
                                        <div class="text-sm mt-1">Esta venta ya fue marcada como <b>PAGADA</b>.</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ACCIONES (pendiente) --}}
            @if($estado === 'pendiente')
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                ¿Qué deseas hacer?
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Puedes dejar una nota (opcional) al aceptar o rechazar.
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <button
                                type="button"
                                onclick="openRespuestaModal('rechazar','{{ route('cliente.cotizaciones.rechazar', $c->id) }}')"
                                class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold"
                            >
                                Rechazar
                            </button>

                            <button
                                type="button"
                                onclick="openRespuestaModal('aceptar','{{ route('cliente.cotizaciones.aceptar', $c->id) }}')"
                                class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-extrabold"
                            >
                                Aceptar
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
