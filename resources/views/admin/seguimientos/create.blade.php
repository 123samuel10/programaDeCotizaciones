{{-- resources/views/admin/seguimientos/create.blade.php --}}
<x-app-layout>
    @php
        $incoterms = $incoterms ?? [];
        $incotermOld = old('incoterm', '');
        $tipoEnvioOld = old('tipo_envio', 'maritimo');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                Crear seguimiento · Venta #{{ $venta->id }}
            </h2>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Cliente: <b>{{ $venta->usuario->name ?? '—' }}</b>
                @if(!empty($venta->usuario?->empresa)) · {{ $venta->usuario->empresa }} @endif
                @if(!empty($venta->usuario?->email)) · {{ $venta->usuario->email }} @endif
            </p>

            <p class="text-xs text-gray-500 dark:text-gray-400">
                <b>ETD</b>: salida estimada · <b>ETA</b>: llegada estimada ·
                <b>AWB</b>: guía aérea (solo si es AÉREO).
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/30 dark:text-red-100">
                    <div class="font-extrabold">Revisa los campos</div>
                    <ul class="list-disc pl-5 text-sm mt-2">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div
                x-data="{
                    incoterm: @js($incotermOld),
                    tipoEnvio: @js($tipoEnvioOld),
                    incoterms: @js($incoterms),
                }"
                class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6"
            >
                <form method="POST" action="{{ route('admin.ventas.seguimiento.store', $venta->id) }}" class="space-y-6">
                    @csrf

                    {{-- ==========================
                         DATOS BASE
                         ========================== --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Proveedor (opcional)</label>
                            <select name="proveedor_id" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">— Sin proveedor —</option>
                                @foreach($proveedores as $p)
                                    <option value="{{ $p->id }}" {{ old('proveedor_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">País destino</label>
                            <input type="text" name="pais_destino" value="{{ old('pais_destino', 'Colombia') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Tipo de envío</label>
                            <select name="tipo_envio" x-model="tipoEnvio"
                                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="maritimo">Marítimo</option>
                                <option value="aereo">Aéreo</option>
                            </select>

                            <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                <template x-if="tipoEnvio === 'maritimo'">
                                    <div>
                                        <div class="font-extrabold">Marítimo</div>
                                        <div class="mt-1">Se trabaja con contenedores, BL, naviera y puertos.</div>
                                    </div>
                                </template>
                                <template x-if="tipoEnvio === 'aereo'">
                                    <div>
                                        <div class="font-extrabold">Aéreo</div>
                                        <div class="mt-1">No usa contenedores. Se controla por AWB, aerolínea, aeropuertos y vuelo.</div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                Incoterm <span class="font-normal text-gray-500">(opcional)</span>
                            </label>

                            <select name="incoterm" x-model="incoterm"
                                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">— Seleccionar —</option>
                                @foreach($incoterms as $k => $meta)
                                    <option value="{{ $k }}">{{ $k }} — {{ $meta['label'] ?? $k }}</option>
                                @endforeach
                            </select>

                            <template x-if="incoterm && incoterms[incoterm]">
                                <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700
                                            dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                    <div class="font-extrabold" x-text="incoterms[incoterm].label ?? incoterm"></div>
                                    <div class="mt-1 leading-relaxed" x-text="incoterms[incoterm].desc ?? ''"></div>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Estado inicial</label>
                            <select name="estado" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @foreach($estados as $k => $label)
                                    <option value="{{ $k }}" {{ old('estado','ordencompra') === $k ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                Flujo: Compra confirmada → En fabricación → Listo → Embarcado/Tránsito → Aduana → Entregado.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Salida estimada (ETD)</label>
                                <input type="date" name="etd" value="{{ old('etd') }}"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Llegada estimada (ETA)</label>
                                <input type="date" name="eta" value="{{ old('eta') }}"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                        </div>
                    </div>

                    {{-- ==========================
                         DETALLES INCOTERM (DINÁMICO)
                         ========================== --}}
                    <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Detalles del Incoterm</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Solo se muestra lo que aplica según el Incoterm seleccionado.
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div x-show="!incoterm"
                                 class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700
                                        dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                Selecciona un Incoterm (FOB/CIF/EXW) para ver campos adicionales.
                            </div>

                            {{-- EXW --}}
                            <div x-cloak x-show="incoterm === 'EXW'" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                            Lugar de retiro <span class="text-red-500">*</span>
                                        </label>
                                        <input name="incoterm_detalles[lugar_retiro]"
                                               value="{{ old('incoterm_detalles.lugar_retiro') }}"
                                               placeholder="Ej: Bodega Shenzhen / Dirección proveedor"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                            Obligatorio en EXW (dónde se recoge la carga).
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Contacto / referencia (opcional)</label>
                                        <input name="incoterm_detalles[contacto_retiro]"
                                               value="{{ old('incoterm_detalles.contacto_retiro') }}"
                                               placeholder="Ej: Sr. Li · +86…"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>
                                </div>
                            </div>

                            {{-- FOB --}}
                            <div x-cloak x-show="incoterm === 'FOB'" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                            Puerto de carga <span class="text-red-500">*</span>
                                        </label>
                                        <input name="incoterm_detalles[puerto_carga]"
                                               value="{{ old('incoterm_detalles.puerto_carga') }}"
                                               placeholder="Ej: Puerto de Shanghái"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                            Obligatorio en FOB (dónde se embarca).
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Fecha listo en puerto (opcional)</label>
                                        <input type="date" name="incoterm_detalles[fecha_listo_puerto]"
                                               value="{{ old('incoterm_detalles.fecha_listo_puerto') }}"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>
                                </div>
                            </div>

                            {{-- CIF --}}
                            <div x-cloak x-show="incoterm === 'CIF'" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                            Puerto de destino <span class="text-red-500">*</span>
                                        </label>
                                        <input name="incoterm_detalles[puerto_destino]"
                                               value="{{ old('incoterm_detalles.puerto_destino') }}"
                                               placeholder="Ej: Cartagena / Buenaventura"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                            Obligatorio en CIF (hasta dónde cubre el vendedor).
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Aseguradora (opcional)</label>
                                        <input name="incoterm_detalles[aseguradora]"
                                               value="{{ old('incoterm_detalles.aseguradora') }}"
                                               placeholder="Ej: Allianz / Mapfre"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Valor seguro (opcional)</label>
                                        <input type="number" step="0.01" min="0" name="incoterm_detalles[valor_seguro]"
                                               value="{{ old('incoterm_detalles.valor_seguro') }}"
                                               placeholder="Ej: 120.00"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>

                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700
                                                dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                        <div class="font-extrabold">Tip CIF</div>
                                        <div class="mt-1">
                                            CIF incluye costo + flete + seguro hasta puerto destino.
                                            Aduana y entrega final normalmente quedan del lado del comprador.
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ==========================
                         CAMPOS AÉREOS (solo si tipo_envio = aereo)
                         ========================== --}}
                    <div x-cloak x-show="tipoEnvio === 'aereo'" class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Datos de envío AÉREO</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Control por AWB, aerolínea, aeropuertos y vuelo.
                                </div>
                            </div>
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 ring-1 ring-blue-100
                                         dark:bg-blue-500/10 dark:text-blue-200 dark:ring-blue-500/20">
                                AÉREO
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">AWB (Air Waybill)</label>
                                <input name="awb" value="{{ old('awb') }}" placeholder="Ej: 123-45678901"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Aerolínea</label>
                                <input name="aerolinea" value="{{ old('aerolinea') }}" placeholder="Ej: Avianca Cargo"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Aeropuerto salida</label>
                                <input name="aeropuerto_salida" value="{{ old('aeropuerto_salida') }}" placeholder="Ej: PVG / Shanghai"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Aeropuerto llegada</label>
                                <input name="aeropuerto_llegada" value="{{ old('aeropuerto_llegada') }}" placeholder="Ej: BOG / Bogotá"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Vuelo (opcional)</label>
                                <input name="vuelo" value="{{ old('vuelo') }}" placeholder="Ej: AV1234"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Tracking URL (opcional)</label>
                                <input name="tracking_url" value="{{ old('tracking_url') }}" placeholder="https://..."
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                        </div>
                    </div>

                    {{-- ==========================
                         OBSERVACIONES
                         ========================== --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Observaciones (opcional)</label>
                        <textarea name="observaciones" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 p-4"
                                  placeholder="Notas internas del proceso...">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <a href="{{ route('admin.ventas.show', $venta->id) }}"
                           class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                                  dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100 text-center">
                            Volver a venta
                        </a>

                        <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                            Crear seguimiento
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
