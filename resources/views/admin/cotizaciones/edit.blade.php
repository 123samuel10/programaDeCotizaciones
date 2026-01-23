<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                    Cotización #{{ $cotizacion->id }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Cliente: {{ $cotizacion->usuario->name ?? '—' }} · {{ $cotizacion->usuario->email ?? '' }}
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

            {{-- AGREGAR LÍNEA (PRODUCTO + CANTIDAD) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Agregar producto a la cotización
                </h3>

                <form method="POST" action="{{ route('admin.cotizaciones.items.store', $cotizacion->id) }}"
                      class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    @csrf

                    <div class="md:col-span-4">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Producto</label>
                        <select name="producto_id" class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100">
                            @foreach($productos as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->marca }} - {{ $p->modelo }} ({{ $p->nombre_producto }})
                                </option>
                            @endforeach
                        </select>
                        @error('producto_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Cantidad</label>
                        <input type="number" name="cantidad" min="1" value="1"
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

            {{-- LISTA DE LÍNEAS --}}
            <div class="space-y-6">
                @forelse($cotizacion->items as $item)

                    @php
                        $producto = $item->producto;
                        $opcionesDisponibles = $producto
                            ? $producto->opciones()->orderBy('categoria')->orderBy('orden')->orderBy('nombre')->get()
                            : collect();

                        $baseVentaLinea = (float)$item->precio_base_venta * (int)$item->cantidad;
                        $baseCostoLinea = (float)$item->precio_base_costo * (int)$item->cantidad;

                        $adVentaLinea = (float)$item->opciones->sum('subtotal_venta');
                        $adCostoLinea = (float)$item->opciones->sum('subtotal_costo');
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">

                        <div class="flex justify-between gap-6 items-start">
                            <div>
                                <div class="text-gray-900 dark:text-gray-100 font-bold text-lg">
                                    {{ $producto->nombre_producto ?? '—' }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $producto->marca ?? '' }} {{ $producto->modelo ?? '' }}
                                </div>

                                {{-- Actualizar cantidad --}}
                                <div class="mt-3 flex flex-wrap gap-2 items-end">
                                    <form method="POST"
                                          action="{{ route('admin.cotizaciones.items.update', [$cotizacion->id, $item->id]) }}"
                                          class="flex gap-2 items-end">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-300">Cantidad</label>
                                            <input type="number" name="cantidad" min="1" value="{{ $item->cantidad }}"
                                                   class="w-28 rounded-xl dark:bg-gray-900 dark:text-gray-100">
                                        </div>

                                        <button class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                                            Actualizar
                                        </button>
                                    </form>

                                    {{--  Eliminar línea --}}
                                    <form method="POST"
                                          action="{{ route('admin.cotizaciones.items.destroy', [$cotizacion->id, $item->id]) }}"
                                          onsubmit="return confirm('¿Eliminar esta línea?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white">
                                            Eliminar línea
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="text-right text-sm whitespace-nowrap">
                                <div>Base Venta: <b>${{ number_format($baseVentaLinea, 2, ',', '.') }}</b></div>
                                <div>Base Costo: <b>${{ number_format($baseCostoLinea, 2, ',', '.') }}</b></div>
                                <div class="mt-2">Adic. Venta: <b>${{ number_format($adVentaLinea, 2, ',', '.') }}</b></div>
                                <div>Adic. Costo: <b>${{ number_format($adCostoLinea, 2, ',', '.') }}</b></div>
                            </div>
                        </div>

                        {{-- Adiciones SOLO para este item --}}
                        <div class="mt-6 border-t dark:border-gray-700 pt-5">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                Adiciones (solo para este producto)
                            </h4>

                            <form method="POST"
                                  action="{{ route('admin.cotizaciones.items.opciones.store', [$cotizacion->id, $item->id]) }}"
                                  class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                @csrf

                                <div class="md:col-span-4">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Opción</label>
                                    <select name="opcion_id" class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100">
                                        @foreach($opcionesDisponibles as $op)
                                            <option value="{{ $op->id }}">{{ $op->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-1">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Cantidad</label>
                                    <input type="number" name="cantidad" min="1" value="1"
                                           class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100">
                                </div>

                                <div class="md:col-span-1 flex items-end">
                                    <button class="w-full px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                                        + Agregar
                                    </button>
                                </div>
                            </form>

                            {{-- Tabla de adiciones del item --}}
                            @if($item->opciones->count())
                                <div class="mt-4 overflow-x-auto">
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
                                            @foreach($item->opciones as $op)
                                                <tr class="border-t dark:border-gray-700">
                                                    <td class="py-2 font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $op->opcion->nombre ?? '—' }}
                                                    </td>
                                                    <td class="py-2 text-right">{{ $op->cantidad }}</td>
                                                    <td class="py-2 text-right">${{ number_format($op->subtotal_venta, 2, ',', '.') }}</td>
                                                    <td class="py-2 text-right">${{ number_format($op->subtotal_costo, 2, ',', '.') }}</td>
                                                    <td class="py-2 text-right">
                                                        <form method="POST"
                                                              action="{{ route('admin.cotizaciones.items.opciones.destroy', [$cotizacion->id, $item->id, $op->id]) }}">
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
                    </div>

                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Aún no hay productos en esta cotización. Agrégalos arriba.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- TOTAL GENERAL --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">TOTAL</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="flex justify-between">
                        <span>Total Venta</span>
                        <b class="text-lg">${{ number_format((float) $cotizacion->total_venta, 2, ',', '.') }}</b>
                    </div>

                    <div class="flex justify-between">
                        <span>Total Costo</span>
                        <b class="text-lg">${{ number_format((float) $cotizacion->total_costo, 2, ',', '.') }}</b>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                    * Total = suma de (base*cantidad + adiciones) por cada línea.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
