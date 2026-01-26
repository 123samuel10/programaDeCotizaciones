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

            <a href="{{ route('admin.ventas.index') }}"
               class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                ← Volver
            </a>
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

            @php
                $estado = $venta->estado_venta ?? 'pendiente_pago';
                $badge = match($estado) {
                    'pagada' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                    'cancelada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                    default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                };
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

            {{-- Actualizar estado --}}
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-gray-100 mb-4">
                    Gestión de venta
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
                    @php
                        $totalItem = (float) $it->subtotal_venta;
                    @endphp

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
