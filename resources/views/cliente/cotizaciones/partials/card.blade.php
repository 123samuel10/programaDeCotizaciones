{{-- resources/views/cliente/cotizaciones/partials/card.blade.php --}}

@php
    use App\Models\Venta;

    $estado = $c->estado ?? 'pendiente';

    $badge = match($estado) {
        'aceptada' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
        'rechazada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
        default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
    };

    $created = optional($c->created_at)->format('Y-m-d H:i');
    $respond = $c->respondida_en
        ? \Carbon\Carbon::parse($c->respondida_en)->format('Y-m-d H:i')
        : null;

    // ✅ Venta asociada (1 venta por cotización)
    $venta = Venta::where('cotizacion_id', $c->id)->first();

    $metodo = $venta->metodo_pago ?? null; // efectivo | transferencia
    $ventaEstado = $venta->estado_venta ?? null;

    $compPath = $venta->comprobante_path ?? null;
    $compEstado = $venta->comprobante_estado ?? null; // pendiente_revision | aceptado | rechazado
    $compNota = $venta->comprobante_nota_admin ?? null;

    $badgeComp = match($compEstado) {
        'aceptado' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
        'rechazado' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
        default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
    };

    // Datos de cuenta desde .env
    $pagoBanco   = env('PAGO_BANCO', 'Banco');
    $pagoCuenta  = env('PAGO_CUENTA', '0000000000');
    $pagoTitular = env('PAGO_TITULAR', 'Heral Enterprises');
@endphp

<div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden shadow-sm">

    {{-- HEADER --}}
    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                        Cotización #{{ $c->id }}
                    </h3>

                    <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 {{ $badge }}">
                        {{ strtoupper($estado) }}
                    </span>
                </div>

                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Creada: {{ $created }}
                    @if($respond)
                        · Respondida: {{ $respond }}
                    @endif
                </div>
            </div>

            <div class="flex gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 ring-1 ring-blue-100 dark:bg-blue-900/20 dark:text-blue-200 dark:ring-blue-900">
                    Total: ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
                </span>
            </div>

        </div>

        {{-- Toggle --}}
        <div class="mt-5">
            <button
                onclick="toggleCotizacion('cot_{{ $c->id }}')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
            >
                Ver / ocultar detalle
            </button>
        </div>
    </div>

    {{-- DETALLE --}}
    <div id="cot_{{ $c->id }}" class="hidden">
        <div class="p-6 space-y-6">

            {{-- ITEMS --}}
            @foreach($c->items as $item)
                @php
                    $p = $item->producto;

                    $baseVentaLinea = (float)$item->precio_base_venta * (int)$item->cantidad;
                    $adicVentaLinea = (float)$item->opciones->sum('subtotal_venta');
                    $totalLinea     = $baseVentaLinea + $adicVentaLinea;
                @endphp

                <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">

                    {{-- Producto --}}
                    <div class="p-5 bg-gray-50 dark:bg-gray-900/30">
                        <div class="flex justify-between gap-4">
                            <div>
                                <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                    {{ $p->nombre_producto ?? 'Producto' }}
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $p->marca ?? '' }} {{ $p->modelo ?? '' }}
                                </div>

                                <div class="mt-2 text-xs font-bold text-gray-700 dark:text-gray-200">
                                    Cantidad: {{ (int)$item->cantidad }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Totales --}}
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                            {{-- Base --}}
                            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Base (precio × cantidad)
                                </div>
                                <div class="mt-1 text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                    ${{ number_format($baseVentaLinea, 2, ',', '.') }}
                                </div>
                                <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                    {{ number_format((float)$item->precio_base_venta, 2, ',', '.') }}
                                    × {{ (int)$item->cantidad }}
                                </div>
                            </div>

                            {{-- Adiciones --}}
                            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Adiciones
                                </div>
                                <div class="mt-1 text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                    ${{ number_format($adicVentaLinea, 2, ',', '.') }}
                                </div>
                            </div>

                            {{-- Total línea --}}
                            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Total del producto
                                </div>
                                <div class="mt-1 text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                    ${{ number_format($totalLinea, 2, ',', '.') }}
                                </div>
                            </div>

                        </div>

                        {{-- Tabla adiciones --}}
                        <div class="mt-5">
                            <h4 class="font-extrabold text-gray-900 dark:text-gray-100 mb-2">
                                Adiciones incluidas
                            </h4>

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
                                                <th class="py-2 px-4 text-right">Cant.</th>
                                                <th class="py-2 px-4 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item->opciones as $op)
                                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                                    <td class="py-2 px-4 font-semibold">
                                                        {{ $op->opcion->nombre ?? '—' }}
                                                    </td>
                                                    <td class="py-2 px-4 text-right">
                                                        {{ (int)$op->cantidad }}
                                                    </td>
                                                    <td class="py-2 px-4 text-right font-bold">
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

            {{-- TOTAL GENERAL --}}
            <div class="p-5 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-sm font-semibold text-blue-900 dark:text-blue-200">
                            Total general
                        </div>
                        <div class="text-xs text-blue-800/70 dark:text-blue-200/70">
                            Total final de la cotización
                        </div>
                    </div>

                    <div class="text-2xl font-extrabold text-blue-900 dark:text-blue-100">
                        ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- ✅ PAGO (solo si está aceptada) --}}
            @if($estado === 'aceptada' && $venta)
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h4 class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                Pago de la venta #{{ $venta->id }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Estado de la venta: <span class="font-bold">{{ strtoupper(str_replace('_',' ', $ventaEstado ?? 'pendiente_pago')) }}</span>
                            </p>
                        </div>

                        @if($compEstado)
                            <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 inline-flex {{ $badgeComp }}">
                                COMPROBANTE: {{ strtoupper(str_replace('_',' ', $compEstado)) }}
                            </span>
                        @endif
                    </div>

                    {{-- Seleccionar método --}}
                    <form method="POST" action="{{ route('cliente.ventas.metodo', $venta->id) }}" class="space-y-3">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer">
                                <input type="radio" name="metodo_pago" value="efectivo" {{ $metodo === 'efectivo' ? 'checked' : '' }}>
                                <div>
                                    <div class="font-extrabold text-gray-900 dark:text-gray-100">Efectivo</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Pagas en efectivo al recibir / acordar.</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 cursor-pointer">
                                <input type="radio" name="metodo_pago" value="transferencia" {{ $metodo === 'transferencia' ? 'checked' : '' }}>
                                <div>
                                    <div class="font-extrabold text-gray-900 dark:text-gray-100">Transferencia</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Transfiere y sube tu comprobante.</div>
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                Guardar método
                            </button>
                        </div>
                    </form>

                    {{-- Si eligió transferencia: datos cuenta + subir comprobante --}}
                    @if($metodo === 'transferencia')
                        <div class="rounded-2xl bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700 p-5 space-y-3">
                            <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                Datos para transferencia
                            </div>

                            <div class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                                <div><span class="font-bold">Banco:</span> {{ $pagoBanco }}</div>
                                <div><span class="font-bold">Cuenta:</span> {{ $pagoCuenta }}</div>
                                <div><span class="font-bold">Titular:</span> {{ $pagoTitular }}</div>
                            </div>

                            {{-- Estado actual del comprobante --}}
                            @if($compPath)
                                <div class="text-sm text-gray-700 dark:text-gray-200">
                                    <span class="font-bold">Comprobante actual:</span>
                                    <a class="text-blue-600 hover:underline font-bold"
                                       href="{{ asset('storage/'.$compPath) }}"
                                       target="_blank">
                                        Ver archivo
                                    </a>
                                </div>
                            @endif

                            @if($compEstado === 'rechazado' && $compNota)
                                <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20 text-red-700 dark:text-red-200">
                                    <div class="font-extrabold">Comprobante rechazado</div>
                                    <div class="text-sm mt-1">{{ $compNota }}</div>
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
                                    Al enviar el comprobante, quedará <b>pendiente de revisión</b> por el administrador.
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
            @endif

            {{-- ACCIONES (aceptar / rechazar) --}}
            @if($estado === 'pendiente')
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                    <button
                        onclick="openRespuestaModal('rechazar','{{ route('cliente.cotizaciones.rechazar', $c->id) }}')"
                        class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold"
                    >
                        Rechazar
                    </button>

                    <button
                        onclick="openRespuestaModal('aceptar','{{ route('cliente.cotizaciones.aceptar', $c->id) }}')"
                        class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-extrabold"
                    >
                        Aceptar
                    </button>
                </div>
            @endif

        </div>
    </div>

</div>
