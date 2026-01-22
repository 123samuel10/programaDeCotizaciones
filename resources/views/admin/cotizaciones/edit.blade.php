{{-- resources/views/admin/cotizaciones/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                    Cotización #{{ $cotizacion->id }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Cliente: {{ $cotizacion->usuario->name ?? '—' }} ·
                    Producto: {{ $cotizacion->producto->nombre_producto ?? '—' }}
                </p>
            </div>

            <a href="{{ route('admin.cotizaciones.index') }}"
               class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-100 dark:border-green-900">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    {{ session('error') }}
                </div>
            @endif

            {{-- PRODUCTO BASE --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 mb-6">
                <div class="flex justify-between gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <b>Marca:</b> {{ $cotizacion->producto->marca }} ·
                            <b>Modelo:</b> {{ $cotizacion->producto->modelo }}
                        </p>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            # Repisas iluminadas: <b>{{ $cotizacion->producto->repisas_iluminadas ?? '—' }}</b> ·
                            Refrigerante: <b>{{ $cotizacion->producto->refrigerante ?? '—' }}</b> ·
                            L: <b>{{ $cotizacion->producto->longitud ?? '—' }}</b> ·
                            P: <b>{{ $cotizacion->producto->profundidad ?? '—' }}</b> ·
                            A: <b>{{ $cotizacion->producto->altura ?? '—' }}</b>
                        </p>
                    </div>

                    <div class="text-right text-sm whitespace-nowrap">
                        <div>Base Venta: <b>${{ number_format($cotizacion->producto->precio_base_venta, 2, ',', '.') }}</b></div>
                        <div>Base Costo: <b>${{ number_format($cotizacion->producto->precio_base_costo, 2, ',', '.') }}</b></div>
                    </div>
                </div>
            </div>

            {{-- AGREGAR ADICIÓN --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Agregar adición
                </h3>

                <form method="POST" action="{{ route('admin.cotizaciones.opciones.store', $cotizacion->id) }}"
                      class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    @csrf

                    <div class="md:col-span-4">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Opción</label>
                        <select name="opcion_id" class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100">
                            @foreach($opcionesDisponibles as $op)
                                <option value="{{ $op->id }}">{{ $op->nombre }}</option>
                            @endforeach
                        </select>
                        @error('opcion_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Cantidad</label>
                        <input type="number" name="cantidad" min="1" value="{{ old('cantidad', 1) }}"
                               class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100">
                        @error('cantidad') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-1 flex items-end">
                        <button class="w-full px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                            + Agregar
                        </button>
                    </div>
                </form>
            </div>

            {{-- ITEMS --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Adiciones en la cotización
                </h3>

                @if($cotizacion->items->count() === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aún no hay adiciones.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 dark:text-gray-300">
                                    <th class="py-2">Opción</th>
                                    <th class="py-2 text-right">Cantidad</th>
                                    <th class="py-2 text-right">Subtotal Venta</th>
                                    <th class="py-2 text-right">Subtotal Costo</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cotizacion->items as $it)
                                    <tr class="border-t dark:border-gray-700">
                                        <td class="py-2 font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $it->opcion->nombre ?? '—' }}
                                        </td>
                                        <td class="py-2 text-right">{{ $it->cantidad }}</td>
                                        <td class="py-2 text-right">${{ number_format($it->subtotal_venta, 2, ',', '.') }}</td>
                                        <td class="py-2 text-right">${{ number_format($it->subtotal_costo, 2, ',', '.') }}</td>
                                        <td class="py-2 text-right">
                                            <form method="POST" action="{{ route('admin.cotizaciones.items.destroy', [$cotizacion->id, $it->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- TOTAL --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">TOTAL</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Total Venta</span>
                            <b class="text-lg">${{ number_format((float) $cotizacion->total_venta, 2, ',', '.') }}</b>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Total Costo</span>
                            <b class="text-lg">${{ number_format((float) $cotizacion->total_costo, 2, ',', '.') }}</b>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                    * El total incluye el precio base del producto + adiciones.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
