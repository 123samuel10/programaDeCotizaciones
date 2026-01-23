<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $producto->nombre_producto }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Precio base del producto + Adiciones disponibles (se aplican en la cotización)
                </p>
            </div>

            <a href="{{ route('admin.productos.index') }}"
               class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                ← Volver
            </a>
        </div>
    </x-slot>

    @php
        // ✅ En PRODUCTOS: solo mostramos PRECIO BASE.
        // Las adiciones NO se suman aquí porque se aplican por línea en la cotización.
        $baseVenta = (float) $producto->precio_base_venta;
        $baseCosto = (float) $producto->precio_base_costo;

        // ✅ Lista PLANA para el SELECT (sin categorías)
       $opcionesSelector = [
    'ESPEJO SUPERIOR FRUVER Y CARNES',
    'EVAPORADOR PARA CARNES CON DESHIELO ELÉCTRICO',
    'REPISA ADICIONAL', // ✅ solo una
    'SKD (SEMI-KNOCKED DOWN)',
    'R290 EVAP PRESURIZADO',
    'R290 UNIDAD CONDENSADORA AIRE',
    'R290 UNIDAD CONDENSADORA AGUA',
    'CO2 EVAP PRESURIZADO',
    'CO2 EVAP CAREL (EEV DRIVER, CONTROL Y SENSORES)',
    'CO2 EVAP DANFOSS (EEV DRIVER, CONTROL Y SENSORES)',
];
    @endphp

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

            {{-- STANDARD (SOLO PRECIO BASE) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm mb-6">
                <div class="flex justify-between items-start gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <b>Marca:</b> {{ $producto->marca }} · <b>Modelo:</b> {{ $producto->modelo }}
                        </p>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <b># Repisas iluminadas:</b> {{ $producto->repisas_iluminadas ?? '—' }} ·
                            Refrigerante: <b>{{ $producto->refrigerante ?? '—' }}</b> ·
                            L: <b>{{ $producto->longitud ?? '—' }}</b> ·
                            P: <b>{{ $producto->profundidad ?? '—' }}</b> ·
                            A: <b>{{ $producto->altura ?? '—' }}</b>
                        </p>

                        <div class="mt-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                Las <b>adiciones</b> no modifican el precio del producto.
                                Se aplican únicamente cuando este producto se agrega a una <b>cotización</b>.
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Base Venta (EXWORKS)</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            ${{ number_format($baseVenta, 2, ',', '.') }}
                        </p>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Base Costo</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            ${{ number_format($baseCosto, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- AGREGAR ADICIÓN (CATÁLOGO DE OPCIONES DEL PRODUCTO) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm mb-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Crear / actualizar adición del producto
                    </h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                        Catálogo del producto
                    </span>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Esto solo define las adiciones disponibles y sus precios. En la cotización decides si se agregan o no.
                </p>

                <form method="POST" action="{{ route('admin.productos.opciones.store', $producto) }}"
                      class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    @csrf

                    <div class="md:col-span-4">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Opción</label>
                        <select name="nombre"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            @foreach($opcionesSelector as $op)
                                <option value="{{ $op }}">{{ $op }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Precio Venta</label>
                        <input name="precio_venta" type="number" step="0.01" value="0"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Precio Costo</label>
                        <input name="precio_costo" type="number" step="0.01" value="0"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    </div>

                    <input type="hidden" name="categoria" value="Adiciones">

                    <div class="md:col-span-6 flex justify-end">
                        <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                            Guardar adición
                        </button>
                    </div>
                </form>
            </div>

            {{-- ADICIONES DISPONIBLES (NO SUMAMOS AL PRODUCTO) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Adiciones disponibles para este producto
                </h3>

                @if($producto->opciones->count() === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Aún no has creado adiciones para este producto.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 dark:text-gray-300">
                                    <th class="py-2">Opción</th>
                                    <th class="py-2 text-right">Venta (unit)</th>
                                    <th class="py-2 text-right">Costo (unit)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($producto->opciones as $opcion)
                                    @php
                                        // Si cargas precios con latest() en el controller, first() será el más reciente.
                                        $p = $opcion->precios->first();
                                        $v = (float)($p->precio_venta ?? 0);
                                        $c = (float)($p->precio_costo ?? 0);
                                    @endphp
                                    <tr class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="py-2 font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $opcion->nombre }}
                                        </td>
                                        <td class="py-2 text-right text-gray-700 dark:text-gray-200">
                                            ${{ number_format($v,2,',','.') }}
                                        </td>
                                        <td class="py-2 text-right text-gray-700 dark:text-gray-200">
                                            ${{ number_format($c,2,',','.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ✅ IMPORTANTE: quitamos el "TOTAL" del producto --}}
            {{-- El total real se calcula en la cotización (por línea: base*cantidad + adiciones). --}}

        </div>
    </div>
</x-app-layout>
