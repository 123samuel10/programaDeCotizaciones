{{-- resources/views/cliente/cotizaciones/partials/detalle-modal.blade.php --}}

@php
    $estado = $c->estado ?? 'pendiente';

    $metodo      = $venta->metodo_pago ?? null; // efectivo | transferencia
    $ventaEstado = $venta->estado_venta ?? null;

    $compPath   = $venta->comprobante_path ?? null;
    $compEstado = $venta->comprobante_estado ?? null; // pendiente_revision | aceptado | rechazado
    $compNota   = $venta->comprobante_nota_admin ?? null;

    $ventaEstadoLabel = match($ventaEstado) {
        'pagada'    => 'Pagada',
        'cancelada' => 'Cancelada',
        default     => 'Pendiente de pago',
    };

    $compEstadoLabel = match($compEstado) {
        'aceptado'           => 'Aprobado',
        'rechazado'          => 'Rechazado',
        'pendiente_revision' => 'Pendiente de revisión',
        default              => null,
    };

    $badgeComp = match($compEstado) {
        'aceptado'  => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
        'rechazado' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
        default     => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
    };

    // Datos cuenta desde .env
    $pagoBanco   = env('PAGO_BANCO', 'Banco');
    $pagoCuenta  = env('PAGO_CUENTA', '0000000000');
    $pagoTitular = env('PAGO_TITULAR', 'Heral Enterprises');

    $autoOpen = (string)request('open') === (string)$c->id
             || (string)session('open_detalle') === (string)$c->id;
@endphp

<div
    x-show="openDetalle"
    x-cloak
    class="fixed inset-0 z-50"
    aria-modal="true"
    role="dialog"
>
    <div class="absolute inset-0 bg-black/50" x-on:click="openDetalle = false"></div>

    <div class="relative h-full w-full flex items-end sm:items-center justify-center p-3 sm:p-6">
        <div class="w-full max-w-5xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700"
             x-transition>

            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                        Detalle · Cotización #{{ $c->id }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Aquí ves productos, adiciones y (si aplica) el pago.
                    </div>
                </div>

                <button type="button"
                        x-on:click="openDetalle = false"
                        class="shrink-0 px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
                               dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-extrabold">
                    Cerrar ✕
                </button>
            </div>

            <div class="max-h-[78vh] overflow-y-auto p-5 space-y-6">
                {{--  Scroll al pago cuando venimos de seleccionar método o subir comprobante --}}
                @if($autoOpen && $venta)
                    <div x-init="
                        setTimeout(() => {
                            const target = document.getElementById('pago-{{ $venta->id }}');
                            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 250);
                    "></div>
                @endif

                {{-- Resumen --}}
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">Resumen</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $c->items->count() }} producto(s) · Total final
                            </div>
                        </div>

                        <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                            ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Productos --}}
                <div class="space-y-3">
                    @foreach($c->items as $item)
                        @php
                            $p = $item->producto;

                            $baseVentaLinea = (float)$item->precio_base_venta * (int)$item->cantidad;
                            $adicVentaLinea = (float)$item->opciones->sum('subtotal_venta');
                            $totalLinea     = $baseVentaLinea + $adicVentaLinea;

                            $repisas = $p->repisas_iluminadas ?? null;
                            $refrigerante = $p->refrigerante ?? null;

                            $partesDim = [];
                            if(!is_null($p->longitud)) $partesDim[] = (int)$p->longitud;
                            if(!is_null($p->profundidad)) $partesDim[] = (int)$p->profundidad;
                            if(!is_null($p->altura)) $partesDim[] = (int)$p->altura;
                            $dim = $partesDim ? implode('×', $partesDim) : '';
                        @endphp

                        <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden"
                             x-data="{ openItem: false }">
                            <button type="button"
                                    class="w-full p-4 bg-white dark:bg-gray-900 flex items-start justify-between gap-4 text-left"
                                    x-on:click="openItem = !openItem">
                                <div class="min-w-0">
                                    <div class="font-extrabold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $p->nombre_producto ?? 'Producto' }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $p->marca ?? '—' }}{{ $p->modelo ? ' · '.$p->modelo : '' }} · Cantidad: {{ (int)$item->cantidad }}
                                    </div>

                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-600 dark:text-gray-300">
                                        @if(!is_null($repisas))
                                            <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                Repisas: <b>{{ $repisas }}</b>
                                            </span>
                                        @endif
                                        @if($refrigerante)
                                            <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                Refrigerante: <b>{{ $refrigerante }}</b>
                                            </span>
                                        @endif
                                        @if($dim !== '')
                                            <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                Dim: <b>{{ $dim }}</b>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="shrink-0 text-right">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                                    <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                        ${{ number_format($totalLinea, 2, ',', '.') }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                        Base: ${{ number_format($baseVentaLinea, 2, ',', '.') }} · Adic: ${{ number_format($adicVentaLinea, 2, ',', '.') }}
                                    </div>

                                    <div class="mt-2 text-xs font-bold text-blue-600 dark:text-blue-300">
                                        <span x-text="openItem ? 'Ocultar' : 'Ver'"></span> detalles
                                    </div>
                                </div>
                            </button>

                            <div x-show="openItem" x-collapse class="p-4 bg-gray-50 dark:bg-gray-800/40 space-y-4">
                                @if(!empty($p->foto))
                                    <button
                                        type="button"
                                        onclick="openProductImageModal('{{ asset('storage/'.$p->foto) }}','{{ addslashes($p->nombre_producto ?? 'Producto') }}','{{ addslashes(($p->marca ?? '—').' · '.($p->modelo ?? '—')) }}')"
                                        class="text-xs font-extrabold px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white"
                                    >
                                        Ver imagen del producto
                                    </button>
                                @endif

                                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                                    <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                        Adiciones
                                    </div>

                                    @if($item->opciones->isEmpty())
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Este producto no tiene adiciones.
                                        </p>
                                    @else
                                        <div class="overflow-x-auto mt-3 rounded-2xl border border-gray-100 dark:border-gray-700">
                                            <table class="min-w-full text-sm">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="py-2 px-4 text-left">Adición</th>
                                                        <th class="py-2 px-4 text-right">Cant.</th>
                                                        <th class="py-2 px-4 text-right">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($item->opciones as $op)
                                                        <tr class="border-t border-gray-100 dark:border-gray-700">
                                                            <td class="py-2 px-4 font-semibold text-gray-900 dark:text-gray-100">
                                                                {{ $op->opcion->nombre ?? '—' }}
                                                            </td>
                                                            <td class="py-2 px-4 text-right">{{ (int)$op->cantidad }}</td>
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

                {{--  PAGO --}}
                @if($estado === 'aceptada' && $venta)
                    <div id="pago-{{ $venta->id }}"
                         class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div>
                                    <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Pago</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Venta #{{ $venta->id }} · {{ $ventaEstadoLabel }}
                                    </div>
                                </div>

                                @if($compEstadoLabel)
                                    <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 {{ $badgeComp }}">
                                        Comprobante: {{ $compEstadoLabel }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-5 space-y-4">
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                    1) Elige método de pago
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    Selecciona una opción y presiona <b>Continuar</b>.
                                </p>

                                <form method="POST" action="{{ route('cliente.ventas.metodo', $venta->id) }}" class="mt-3 space-y-3">
                                    @csrf

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <input class="mt-1" type="radio" name="metodo_pago" value="efectivo" {{ $metodo === 'efectivo' ? 'checked' : '' }}>
                                            <div>
                                                <div class="font-extrabold text-gray-900 dark:text-gray-100">Efectivo</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Pagas al recibir / acordar entrega.</div>
                                            </div>
                                        </label>

                                        <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <input class="mt-1" type="radio" name="metodo_pago" value="transferencia" {{ $metodo === 'transferencia' ? 'checked' : '' }}>
                                            <div>
                                                <div class="font-extrabold text-gray-900 dark:text-gray-100">Transferencia</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Transfiere y sube comprobante.</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="flex justify-end">
                                        <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                            Continuar →
                                        </button>
                                    </div>
                                </form>
                            </div>

                            @if($metodo === 'efectivo')
                                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-green-50 dark:bg-green-900/20 p-4">
                                    <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                        2) Pago en efectivo
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">
                                        Realizas el pago al recibir / acordar la entrega con la empresa.
                                    </p>
                                </div>
                            @endif

                            @if($metodo === 'transferencia')
                                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 p-4 space-y-4">
                                    <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                        2) Realiza la transferencia
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                        <div class="rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Banco</div>
                                            <div class="font-extrabold text-gray-900 dark:text-gray-100 flex items-center justify-between gap-2">
                                                <span>{{ $pagoBanco }}</span>
                                                <button type="button"
                                                        onclick="navigator.clipboard.writeText('{{ $pagoBanco }}')"
                                                        class="px-2 py-1 rounded-lg text-xs font-extrabold bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    Copiar
                                                </button>
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Cuenta</div>
                                            <div class="font-extrabold text-gray-900 dark:text-gray-100 flex items-center justify-between gap-2">
                                                <span>{{ $pagoCuenta }}</span>
                                                <button type="button"
                                                        onclick="navigator.clipboard.writeText('{{ $pagoCuenta }}')"
                                                        class="px-2 py-1 rounded-lg text-xs font-extrabold bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    Copiar
                                                </button>
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Titular</div>
                                            <div class="font-extrabold text-gray-900 dark:text-gray-100 flex items-center justify-between gap-2">
                                                <span>{{ $pagoTitular }}</span>
                                                <button type="button"
                                                        onclick="navigator.clipboard.writeText('{{ $pagoTitular }}')"
                                                        class="px-2 py-1 rounded-lg text-xs font-extrabold bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    Copiar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="font-extrabold text-gray-900 dark:text-gray-100 pt-1">
                                        3) Sube tu comprobante
                                    </div>

                                    @if($compPath)
                                        <div class="text-sm text-gray-700 dark:text-gray-200">
                                            <span class="font-bold">Comprobante actual:</span>
                                            <a class="text-blue-600 hover:underline font-extrabold"
                                               href="{{ asset('storage/'.$compPath) }}" target="_blank">
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
                                            Al enviar tu comprobante quedará <b>pendiente de revisión</b>.
                                        </p>
                                    @else
                                        <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-500/10 border border-green-100 dark:border-green-500/20 text-green-800 dark:text-green-200">
                                            <div class="font-extrabold">Pago aprobado</div>
                                            <div class="text-sm mt-1">Esta venta ya está marcada como <b>PAGADA</b>.</div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-900 flex justify-end">
                <button type="button"
                        x-on:click="openDetalle = false"
                        class="px-5 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                               dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                    Listo
                </button>
            </div>
        </div>
    </div>
</div>
