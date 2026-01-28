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
 {{-- AGREGAR LÍNEA (PRODUCTO + CANTIDAD) --}}
<div
    class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 mb-6"
    x-data="{
        productos: @js($productos->map(fn($p) => [
            'id' => $p->id,
            'marca' => $p->marca,
            'modelo' => $p->modelo,
            'nombre_producto' => $p->nombre_producto,
            'descripcion' => $p->descripcion,
            'foto' => $p->foto,
            'repisas_iluminadas' => $p->repisas_iluminadas,
            'refrigerante' => $p->refrigerante,
            'longitud' => $p->longitud,
            'profundidad' => $p->profundidad,
            'altura' => $p->altura,
            'precio_base_venta' => $p->precio_base_venta,
            'precio_base_costo' => $p->precio_base_costo,
        ])),
        seleccionadoId: '',
        get seleccionado(){
            return this.productos.find(p => String(p.id) === String(this.seleccionadoId)) || null;
        },
        init(){
            this.$nextTick(() => {
                const el = this.$refs.selectProducto;
                if (el && el.value) this.seleccionadoId = el.value;
            });
        }
    }"
>
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        Agregar producto a la cotización
    </h3>

    @php
        $hayDisponibles = $productos->whereNotIn('id', $productosAgregadosIds ?? [])->count() > 0;
    @endphp

    @if(!$hayDisponibles)
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Ya agregaste todos los productos disponibles a esta cotización.
            </p>
        </div>
    @else
        <form method="POST" action="{{ route('admin.cotizaciones.items.store', $cotizacion->id) }}"
              class="grid grid-cols-1 md:grid-cols-6 gap-4">
            @csrf

            <div class="md:col-span-4">
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Producto</label>

                <select
                    x-ref="selectProducto"
                    x-model="seleccionadoId"
                    name="producto_id"
                    class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100"
                >
                    @foreach($productos as $p)
                        @continue(in_array($p->id, $productosAgregadosIds ?? []))
                        <option value="{{ $p->id }}">
                            {{ $p->marca }} - {{ $p->modelo }} ({{ $p->nombre_producto }})
                        </option>
                    @endforeach
                </select>

                @error('producto_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                {{-- FICHA DEL PRODUCTO SELECCIONADO --}}
                <div class="mt-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                    <template x-if="seleccionado">
                        <div class="space-y-3">

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="text-sm font-extrabold text-gray-900 dark:text-gray-100"
                                      x-text="seleccionado.nombre_producto"></span>

                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-50 text-blue-700
                                             dark:bg-blue-500/10 dark:text-blue-300">
                                    <span x-text="seleccionado.marca"></span>
                                    <span class="opacity-60">·</span>
                                    <span x-text="seleccionado.modelo"></span>
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
                                <div class="rounded-xl bg-white/70 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Repisas iluminadas</p>
                                    <p class="font-bold text-gray-900 dark:text-gray-100"
                                       x-text="seleccionado.repisas_iluminadas ?? '—'"></p>
                                </div>

                                <div class="rounded-xl bg-white/70 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Refrigerante</p>
                                    <p class="font-bold text-gray-900 dark:text-gray-100"
                                       x-text="seleccionado.refrigerante ?? '—'"></p>
                                </div>

                                <div class="rounded-xl bg-white/70 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Dimensiones (L×P×A)</p>
                                    <p class="font-bold text-gray-900 dark:text-gray-100">
                                        <span x-text="seleccionado.longitud ?? '—'"></span>
                                        <span class="opacity-60">×</span>
                                        <span x-text="seleccionado.profundidad ?? '—'"></span>
                                        <span class="opacity-60">×</span>
                                        <span x-text="seleccionado.altura ?? '—'"></span>
                                    </p>
                                </div>

                                <div class="rounded-xl bg-white/70 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Precios base</p>
                                    <p class="font-bold text-gray-900 dark:text-gray-100">
                                        V: <span x-text="'$' + Number(seleccionado.precio_base_venta || 0).toLocaleString('es-CO')"></span>
                                        <span class="opacity-60">·</span>
                                        C: <span x-text="'$' + Number(seleccionado.precio_base_costo || 0).toLocaleString('es-CO')"></span>
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-xl bg-white/70 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Descripción</p>
                                <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed"
                                   x-text="seleccionado.descripcion ?? 'Sin descripción'"></p>
                            </div>

                        </div>
                    </template>

                    <template x-if="!seleccionado">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Selecciona un producto para ver su ficha.
                        </p>
                    </template>
                </div>
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
    @endif
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

                                    {{-- Eliminar línea (BOTÓN PRO + MODAL) --}}
                                    <button type="button"
                                            x-data
                                            x-on:click="$dispatch('open-delete-modal', {
                                                action: '{{ route('admin.cotizaciones.items.destroy', [$cotizacion->id, $item->id]) }}',
                                                titulo: 'Eliminar línea de cotización',
                                                detalle: 'Se eliminará: {{ addslashes($producto->nombre_producto ?? 'Producto') }}. Esta acción no se puede deshacer.'
                                            })"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                                                   bg-red-600 hover:bg-red-700 text-white font-semibold
                                                   shadow-sm hover:shadow transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862A2 2 0 015.867 19.142L5 7m5 4v6m4-6v6M9 7h6m-7 0V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                                        </svg>
                                        Eliminar línea
                                    </button>
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

    {{-- MODAL PRO GLOBAL (Eliminar línea) --}}
    <div
        x-data="{
            open:false,
            action:'',
            titulo:'Eliminar',
            detalle:'',
            abrir(payload){
                this.action = payload.action || '';
                this.titulo = payload.titulo || 'Confirmar eliminación';
                this.detalle = payload.detalle || 'Esta acción no se puede deshacer.';
                this.open = true;
                this.$nextTick(() => this.$refs.btnCancelar?.focus());
            }
        }"
        x-on:open-delete-modal.window="abrir($event.detail)"
        x-on:keydown.escape.window="open=false"
        x-cloak
    >
        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 bg-black/55 backdrop-blur-sm z-50"
            x-on:click="open=false"
        ></div>

        {{-- Modal --}}
        <div
            x-show="open"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                {{-- Header --}}
                <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v3m0 4h.01M10.29 3.86l-8.02 14A2 2 0 004 21h16a2 2 0 001.73-3.14l-8.02-14a2 2 0 00-3.46 0z"/>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-lg font-extrabold text-gray-900 dark:text-gray-100" x-text="titulo"></h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300" x-text="detalle"></p>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-5">
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-800 p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-200">
                            Si continúas, se eliminará la línea y sus adiciones asociadas (si aplica).
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-5 pt-0 flex gap-3">
                    <button
                        type="button"
                        x-ref="btnCancelar"
                        x-on:click="open=false"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200
                               dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold transition"
                    >
                        Cancelar
                    </button>

                    <form class="flex-1" method="POST" x-bind:action="action">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="w-full px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700
                                   text-white font-extrabold shadow-sm hover:shadow transition
                                   focus:outline-none focus:ring-2 focus:ring-red-500/40"
                        >
                            Sí, eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<form method="POST" action="{{ route('admin.cotizaciones.enviarCorreo', $cotizacion->id) }}">
    @csrf
    <button class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">
        Enviar por correo
    </button>
</form>


</x-app-layout>
