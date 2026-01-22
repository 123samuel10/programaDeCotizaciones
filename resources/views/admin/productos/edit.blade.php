<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $producto->nombre_producto }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Standard (EXWORKS) + Adiciones (como Excel) → Total
                </p>
            </div>

            <a href="{{ route('admin.productos.index') }}"
               class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                ← Volver
            </a>
        </div>
    </x-slot>

    @php
        $opVenta = $producto->opciones->sum(fn($o)=> $o->precios->first()->precio_venta ?? 0);
        $opCosto = $producto->opciones->sum(fn($o)=> $o->precios->first()->precio_costo ?? 0);

        $baseVenta = (float) $producto->precio_base_venta;
        $baseCosto = (float) $producto->precio_base_costo;

        $totalVenta = $baseVenta + $opVenta;
        $totalCosto = $baseCosto + $opCosto;

        // ✅ Lista PLANA para el SELECT (sin categorías)
        $opcionesSelector = [
            'ESPEJO SUPERIOR FRUVER Y CARNES',
            'EVAPORADOR PARA CARNES CON DESHIELO ELÉCTRICO',
            '1 REPISA ADICIONAL',
            '2 REPISAS ADICIONALES',
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

            {{-- STANDARD --}}
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
                    </div>

                    <div class="text-right">
                        <p class="text-sm">Venta: <b>${{ number_format($baseVenta, 2, ',', '.') }}</b></p>
                        <p class="text-sm">Costo: <b>${{ number_format($baseCosto, 2, ',', '.') }}</b></p>
                    </div>
                </div>
            </div>

            {{-- AGREGAR ADICIÓN (SELECT ÚNICO) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Agregar adición (selector)
                    </h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                        Selecciona y agrega
                    </span>
                </div>

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

                    {{-- Si tu controller guarda categoria, mandamos una fija --}}
                    <input type="hidden" name="categoria" value="Adiciones">

                    <div class="md:col-span-6 flex justify-end">
                        <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                            + Agregar
                        </button>
                    </div>
                </form>
            </div>

            {{-- ADICIONES AGREGADAS (tabla limpia) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Adiciones agregadas
                </h3>

                @if($producto->opciones->count() === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Aún no has agregado adiciones.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 dark:text-gray-300">
                                    <th class="py-2">Opción</th>
                                    <th class="py-2 text-right">Venta</th>
                                    <th class="py-2 text-right">Costo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($producto->opciones as $opcion)
                                    @php
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

            {{-- TOTAL --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">TOTAL</h3>
                <div class="text-sm text-gray-700 dark:text-gray-200 space-y-2">
                    <div class="flex justify-between">
                        <span>Total Venta</span>
                        <b>${{ number_format($totalVenta,2,',','.') }}</b>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Costo</span>
                        <b>${{ number_format($totalCosto,2,',','.') }}</b>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
