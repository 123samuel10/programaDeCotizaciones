{{-- resources/views/admin/ventas/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white leading-tight">
                    Venta #{{ $venta->id }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Cliente: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $venta->usuario->name ?? '—' }}</span>
                    · Cotización: <span class="font-semibold">#{{ $venta->cotizacion_id }}</span>
                </p>
            </div>


            @php
    $seg = $venta->seguimiento;
@endphp

<div class="flex gap-2">
    @if($seg)
        <a href="{{ route('admin.seguimientos.show', $seg->id) }}"
           class="px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                  dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
            Abrir seguimiento
        </a>
    @else
        <a href="{{ route('admin.ventas.seguimiento.create', $venta->id) }}"
           class="px-4 py-2 rounded-xl bg-blue-600 text-white font-extrabold hover:bg-blue-700">
            Crear seguimiento
        </a>
    @endif

    <a href="{{ route('admin.ventas.index') }}"
       class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
        ← Volver
    </a>
</div>

        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 border border-green-200
                            dark:bg-green-900/30 dark:text-green-100 dark:border-green-900">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200
                            dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $estado = $venta->estado_venta ?? 'pendiente_pago';
                $badge = match($estado) {
                    'pagada' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                    'cancelada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                    default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                };

                $metodo = $venta->metodo_pago ?: '—';
                $ref = $venta->referencia_pago ?? null;

                $compPath = $venta->comprobante_path ?? null;
                $compEstado = $venta->comprobante_estado ?? null; // pendiente_revision | aceptado | rechazado
                $compSubido = $venta->comprobante_subido_en ?? null;
                $compNota = $venta->comprobante_nota_admin ?? null;

                $tieneComp = !empty($compPath);

                $badgeComp = match($compEstado) {
                    'aceptado' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                    'rechazado' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                    default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                };

                // Datos cuenta desde .env (para mostrarle al admin qué vio el cliente)
                $pagoBanco   = env('PAGO_BANCO', 'Banco');
                $pagoCuenta  = env('PAGO_CUENTA', '0000000000');
                $pagoTitular = env('PAGO_TITULAR', 'Heral Enterprises');

                // Para detectar si es imagen o pdf (para vista previa)
                $ext = $tieneComp ? strtolower(pathinfo($compPath, PATHINFO_EXTENSION)) : '';
                $isImage = in_array($ext, ['jpg','jpeg','png','webp']);
                $isPdf = ($ext === 'pdf');
            @endphp

            {{-- Panel resumen --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 {{ $badge }}">
                            {{ strtoupper(str_replace('_',' ', $estado)) }}
                        </span>

                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            Creada: <span class="font-semibold">{{ optional($venta->created_at)->format('Y-m-d H:i') }}</span>
                            @if($venta->pagada_en)
                                · Pagada: <span class="font-semibold">{{ \Carbon\Carbon::parse($venta->pagada_en)->format('Y-m-d H:i') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Total venta</div>
                        <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                            ${{ number_format((float)$venta->total_venta, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                @if($venta->nota_cliente)
                    <div class="mt-5 p-4 rounded-2xl bg-blue-50 text-blue-800 border border-blue-100
                                dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-900">
                        <div class="font-semibold mb-1">Nota del cliente</div>
                        <p class="text-sm">{{ $venta->nota_cliente }}</p>
                    </div>
                @endif
            </div>

            {{--  BLOQUE PRO DE PAGO + COMPROBANTE --}}
{{--  BLOQUE PRO DE PAGO + COMPROBANTE (CORREGIDO) --}}
<div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                Pago del cliente
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Método elegido y evidencia (si aplica).
            </p>
        </div>

        {{-- Badge de comprobante SOLO si aplica (transferencia + tiene archivo) --}}
        @if(($venta->metodo_pago ?? null) === 'transferencia' && $tieneComp)
            <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 inline-flex {{ $badgeComp }}">
                COMPROBANTE: {{ strtoupper(str_replace('_',' ', $compEstado ?: 'pendiente_revision')) }}
            </span>
        @endif
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Info método --}}
        <div class="lg:col-span-5 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Método</div>

            @if(empty($venta->metodo_pago))
                <div class="mt-1 text-lg font-extrabold text-gray-900 dark:text-gray-100">
                    — Sin definir —
                </div>
                <div class="mt-3 p-4 rounded-2xl bg-yellow-50 border border-yellow-100 text-yellow-800
                            dark:bg-yellow-500/10 dark:border-yellow-500/20 dark:text-yellow-200">
                    <div class="font-extrabold">El cliente aún no ha elegido método</div>
                    <div class="text-sm mt-1">Cuando elija “transferencia” o “efectivo”, se verá aquí.</div>
                </div>
            @else
                <div class="mt-1 text-lg font-extrabold text-gray-900 dark:text-gray-100">
                    {{ strtoupper($metodo) }}
                </div>

                @if($ref)
                    <div class="mt-3 text-sm text-gray-700 dark:text-gray-200">
                        <span class="font-bold">Referencia:</span> {{ $ref }}
                    </div>
                @endif

                {{-- Si es transferencia, mostramos datos de cuenta --}}
                @if($metodo === 'transferencia')
                    <div class="mt-4 rounded-2xl bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700 p-4">
                        <div class="font-extrabold text-gray-900 dark:text-gray-100">
                            Datos de cuenta (lo que ve el cliente)
                        </div>
                        <div class="mt-2 text-sm text-gray-700 dark:text-gray-200 space-y-1">
                            <div><span class="font-bold">Banco:</span> {{ $pagoBanco }}</div>
                            <div><span class="font-bold">Cuenta:</span> {{ $pagoCuenta }}</div>
                            <div><span class="font-bold">Titular:</span> {{ $pagoTitular }}</div>
                        </div>
                    </div>
                @endif

                {{-- Si es efectivo, aclaración PRO --}}
                @if($metodo === 'efectivo')
                    <div class="mt-4 p-4 rounded-2xl bg-green-50 border border-green-100 text-green-800
                                dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-200">
                        <div class="font-extrabold">Pago en efectivo</div>
                        <div class="text-sm mt-1">
                            En efectivo <b>no se solicita comprobante</b>. La confirmación se hace cuando se registre el pago.
                        </div>
                    </div>
                @endif

                @if($tieneComp && $compSubido)
                    <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                        Subido: {{ \Carbon\Carbon::parse($compSubido)->format('Y-m-d H:i') }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Evidencia: SOLO si es transferencia --}}
        <div class="lg:col-span-7 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Evidencia</div>
                    <div class="mt-1 font-extrabold text-gray-900 dark:text-gray-100">
                        Comprobante
                    </div>
                </div>

                @if($metodo === 'transferencia' && $tieneComp)
                    <a href="{{ asset('storage/'.$compPath) }}" target="_blank"
                       class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
                              dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-extrabold">
                        Abrir archivo
                    </a>
                @endif
            </div>

            {{-- Si NO es transferencia, no mostramos comprobante --}}
            @if($metodo !== 'transferencia')
                <div class="mt-4 p-4 rounded-2xl bg-gray-50 border border-gray-200 text-gray-700
                            dark:bg-gray-900/30 dark:border-gray-700 dark:text-gray-200">
                    <div class="font-extrabold">No aplica comprobante</div>
                    <div class="text-sm mt-1">
                        El comprobante solo se solicita cuando el método de pago es <b>transferencia</b>.
                    </div>
                </div>

            @else
                {{-- Aquí sí aplica transferencia: mostrar si hay o no hay --}}
                @if(!$tieneComp)
                    <div class="mt-4 p-4 rounded-2xl bg-yellow-50 border border-yellow-100 text-yellow-800
                                dark:bg-yellow-500/10 dark:border-yellow-500/20 dark:text-yellow-200">
                        <div class="font-extrabold">Pendiente: sin comprobante</div>
                        <div class="text-sm mt-1">El cliente aún no ha subido evidencia.</div>
                    </div>
                @else
                    {{-- Preview --}}
                    <div class="mt-4">
                        @if($isImage)
                            <img src="{{ asset('storage/'.$compPath) }}"
                                 alt="Comprobante"
                                 class="w-full max-h-[420px] object-contain rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                        @elseif($isPdf)
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <iframe src="{{ asset('storage/'.$compPath) }}" class="w-full h-[420px]"></iframe>
                            </div>
                        @else
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                Archivo subido. Usa “Abrir archivo” para verlo.
                            </div>
                        @endif
                    </div>

                    @if($compEstado === 'rechazado' && $compNota)
                        <div class="mt-4 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-800
                                    dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-200">
                            <div class="font-extrabold">Última revisión: RECHAZADO</div>
                            <div class="text-sm mt-1">{{ $compNota }}</div>
                        </div>
                    @endif
                @endif

                {{-- Acciones de revisión: SOLO si transferencia + tiene comprobante + pendiente_revision --}}
                @if($tieneComp && ($compEstado ?? null) === 'pendiente_revision' && $estado !== 'pagada')
                    <div class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-5">
                        <div class="font-extrabold text-gray-900 dark:text-gray-100">
                            Revisión del comprobante
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Aprueba para marcar la venta como <b>PAGADA</b>. Si rechazas, escribe el motivo.
                        </p>

                        <div class="mt-4 grid grid-cols-1 lg:grid-cols-12 gap-3">
                            {{-- Aprobar --}}
                            <form method="POST" action="{{ route('admin.ventas.decision', $venta->id) }}" class="lg:col-span-4">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="decision" value="aceptar">
                                <button class="w-full px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-extrabold">
                                    Aprobar comprobante
                                </button>
                            </form>

                            {{-- Rechazar --}}
                            <form method="POST" action="{{ route('admin.ventas.decision', $venta->id) }}"
                                  class="lg:col-span-8 grid grid-cols-1 lg:grid-cols-12 gap-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="decision" value="rechazar">

                                <div class="lg:col-span-9">
                                    <input
                                        type="text"
                                        name="nota"
                                        maxlength="1000"
                                        placeholder="Motivo del rechazo (ej: imagen borrosa, falta valor, no coincide fecha...)"
                                        class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        required
                                    >
                                </div>

                                <div class="lg:col-span-3">
                                    <button class="w-full px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold">
                                        Rechazar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>


            {{-- Actualizar estado (manual) --}}
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-gray-100 mb-4">
                    Gestión de venta (manual)
                </h3>

                <form method="POST" action="{{ route('admin.ventas.update', $venta->id) }}" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    @csrf
                    @method('PUT')

                    <div class="lg:col-span-3">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Estado</label>
                        <select name="estado_venta" class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100">
                            <option value="pendiente_pago" {{ $estado==='pendiente_pago' ? 'selected' : '' }}>Pendiente pago</option>
                            <option value="pagada" {{ $estado==='pagada' ? 'selected' : '' }}>Pagada</option>
                            <option value="cancelada" {{ $estado==='cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>

                    <div class="lg:col-span-3">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Método de pago (opcional)</label>
                        <input name="metodo_pago" value="{{ old('metodo_pago', $venta->metodo_pago) }}"
                               class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100"
                               placeholder="Transferencia, efectivo, etc.">
                    </div>

                    <div class="lg:col-span-5">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Notas internas</label>
                        <input name="notas_internas" value="{{ old('notas_internas', $venta->notas_internas) }}"
                               class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100"
                               placeholder="Seguimiento, acuerdos, etc.">
                    </div>

                    <div class="lg:col-span-1 flex items-end">
                        <button class="w-full px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Detalle: items + adiciones --}}
            <div class="mt-6 space-y-6">
                @foreach($venta->items as $it)
                    @php $totalItem = (float) $it->subtotal_venta; @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl border dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="font-extrabold text-gray-900 dark:text-gray-100 text-lg">
                                        {{ $it->nombre_producto ?? 'Producto' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $it->marca ?? '' }} {{ $it->modelo ?? '' }}
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Cantidad</div>
                                    <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                        {{ (int)$it->cantidad }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                    Incluye adiciones y cantidad.
                                </div>
                                <div class="text-xl font-extrabold text-gray-900 dark:text-gray-100">
                                    ${{ number_format($totalItem, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h4 class="font-extrabold text-gray-900 dark:text-gray-100">
                                    Adiciones
                                </h4>
                                <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700
                                             dark:bg-gray-700/60 dark:text-gray-200">
                                    {{ $it->opciones->count() }} adición(es)
                                </span>
                            </div>

                            @if($it->opciones->count() === 0)
                                <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                    Este producto no tiene adiciones.
                                </div>
                            @else
                                <div class="mt-4 overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                                        <tr class="text-left text-gray-600 dark:text-gray-300">
                                            <th class="py-2 px-4">Adición</th>
                                            <th class="py-2 px-4 text-right">Cant.</th>
                                            <th class="py-2 px-4 text-right">Subtotal</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($it->opciones as $op)
                                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                                <td class="py-3 px-4 font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $op->nombre_opcion ?? '—' }}
                                                </td>
                                                <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-200">
                                                    {{ (int)$op->cantidad }}
                                                </td>
                                                <td class="py-3 px-4 text-right font-extrabold text-gray-900 dark:text-gray-100">
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
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
