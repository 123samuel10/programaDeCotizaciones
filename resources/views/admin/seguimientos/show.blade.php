{{-- resources/views/admin/seguimientos/show.blade.php --}}
<x-app-layout>
    @php
        /**
         * ==========================
         * Helpers y datos seguros
         * ==========================
         */

        // Flujo típico (para barra de progreso)
        $pasos = ['ordencompra','produccion','listoparaembarque','embarcado','entransito','arriboapuerto','aduana','liberado','entregado','cerrado'];
        $estadoKey = $seguimiento->estado ?? 'ordencompra';
        $idx = array_search($estadoKey, $pasos);
        $idx = ($idx === false) ? 0 : $idx;
        $progreso = (int) round((($idx + 1) / count($pasos)) * 100);

        // Días para ETA (si existe)
        $diasParaLlegar = null;
        if ($seguimiento->eta) {
            $diasParaLlegar = now()->startOfDay()->diffInDays($seguimiento->eta->startOfDay(), false);
        }

        // Etiquetas
        $venta   = $seguimiento->venta ?? null;
        $cliente = $venta?->usuario ?? null;

        $tipoEnvio = $seguimiento->tipo_envio ?? 'maritimo';
        $tipoEnvioLabel = $tipoEnvio === 'aereo' ? 'AÉREO' : 'MARÍTIMO';

        $badgeEstado = 'bg-blue-50 text-blue-700 ring-blue-100 dark:bg-blue-500/10 dark:text-blue-200 dark:ring-blue-500/20';
        if (in_array($estadoKey, ['entregado','cerrado'])) {
            $badgeEstado = 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20';
        } elseif (in_array($estadoKey, ['aduana','arriboapuerto'])) {
            $badgeEstado = 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20';
        }

        // Incoterm
        $incoterms = $incoterms ?? [];
        $incotermSel = $seguimiento->incoterm ?? '';
        $incotermMeta = $incoterms[$incotermSel] ?? null;

        // Detalles incoterm (json)
        $incotermDetalles = $seguimiento->incoterm_detalles ?? [];
        if (!is_array($incotermDetalles)) $incotermDetalles = [];

        // Eventos ordenados (si no viene ya)
        $eventos = $seguimiento->eventos ?? collect();
        if ($eventos instanceof \Illuminate\Support\Collection) {
            $eventos = $eventos->sortByDesc('fecha_evento');
        }

        // Contenedores
        $contenedores = $seguimiento->contenedores ?? collect();
        if ($contenedores instanceof \Illuminate\Support\Collection) {
            $contenedores = $contenedores->sortByDesc('id');
        }
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                        Seguimiento · Venta #{{ $venta?->id ?? '—' }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Cliente:
                        <span class="font-bold text-gray-800 dark:text-gray-100">{{ $cliente?->name ?? '—' }}</span>
                        @if(!empty($cliente?->empresa)) · {{ $cliente->empresa }} @endif
                        @if(!empty($cliente?->email)) · {{ $cliente->email }} @endif
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.seguimientos.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                              dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100">
                        ← Volver
                    </a>
                    <a href="{{ route('admin.ventas.show', $venta?->id ?? 0) }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                              dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        Ver venta
                    </a>
                </div>
            </div>

            {{-- Barra de progreso --}}
            <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold ring-1 {{ $badgeEstado }}">
                            {{ ($estados[$estadoKey] ?? $estadoKey) }}
                        </span>

                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold ring-1
                                     bg-gray-100 text-gray-900 dark:bg-gray-900/40 dark:text-gray-100">
                            {{ $tipoEnvioLabel }}
                        </span>

                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Actualizado: {{ $seguimiento->updated_at?->format('d/m/Y H:i') ?? '—' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        @if(!is_null($diasParaLlegar))
                            <div class="text-xs font-bold">
                                @if($diasParaLlegar > 0)
                                    <span class="text-gray-600 dark:text-gray-300">ETA:</span>
                                    <span class="text-blue-600 dark:text-blue-300 font-extrabold">{{ $diasParaLlegar }}</span>
                                    <span class="text-gray-600 dark:text-gray-300">día(s) restantes</span>
                                @elseif($diasParaLlegar === 0)
                                    <span class="text-green-700 dark:text-green-200 font-extrabold">ETA: llega hoy</span>
                                @else
                                    <span class="text-red-700 dark:text-red-200 font-extrabold">
                                        ETA vencida hace {{ abs($diasParaLlegar) }} día(s)
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex justify-between text-[11px] text-gray-500 dark:text-gray-400 mb-2">
                        <span>Progreso</span>
                        <span class="font-extrabold text-gray-700 dark:text-gray-200">{{ $progreso }}%</span>
                    </div>

                    <div class="h-3 rounded-full bg-gray-100 dark:bg-gray-900/40 overflow-hidden">
                        <div class="h-3 rounded-full bg-blue-600" style="width: {{ $progreso }}%"></div>
                    </div>

                    <div class="mt-3 grid grid-cols-2 sm:grid-cols-5 gap-2 text-[11px]">
                        @foreach($pasos as $i => $p)
                            @php
                                $activo = $i <= $idx;
                                $pill = $activo
                                    ? 'bg-blue-50 text-blue-700 ring-blue-100 dark:bg-blue-500/10 dark:text-blue-200 dark:ring-blue-500/20'
                                    : 'bg-gray-50 text-gray-600 ring-gray-100 dark:bg-gray-900/40 dark:text-gray-300 dark:ring-gray-700';
                            @endphp
                            <span class="inline-flex justify-center px-2 py-1 rounded-full ring-1 font-bold {{ $pill }}">
                                {{ $estados[$p] ?? $p }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-900 dark:bg-green-900/30 dark:text-green-100">
                    <div class="font-extrabold">{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/30 dark:text-red-100">
                    <div class="font-extrabold">{{ session('error') }}</div>
                </div>
            @endif
            @if($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/30 dark:text-red-100">
                    <div class="font-extrabold">Revisa los campos</div>
                    <ul class="list-disc pl-5 text-sm mt-2">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- ==========================
                     COLUMNA IZQUIERDA (Resumen + Update)
                     ========================== --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Resumen --}}
                    <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Resumen</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Datos clave del envío, fechas e incoterm.
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Total venta</div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                    ${{ number_format((float)($venta?->total_venta ?? 0), 2, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Proveedor</div>
                                <div class="mt-1 font-extrabold text-gray-900 dark:text-gray-100">
                                    {{ $seguimiento->proveedor?->nombre ?? '—' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Destino: {{ $seguimiento->pais_destino ?? '—' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Fechas</div>
                                <div class="mt-1 text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                    ETD: {{ $seguimiento->etd?->format('d/m/Y') ?? '—' }}
                                </div>
                                <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                    ETA: {{ $seguimiento->eta?->format('d/m/Y') ?? '—' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Incoterm</div>
                                <div class="mt-1 font-extrabold text-gray-900 dark:text-gray-100">
                                    {{ $seguimiento->incoterm ?? '—' }}
                                </div>

                                @if($incotermMeta)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $incotermMeta['label'] ?? '' }}
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Tipo envío</div>
                                <div class="mt-1 font-extrabold text-gray-900 dark:text-gray-100">
                                    {{ $tipoEnvioLabel }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Estado: {{ $estados[$estadoKey] ?? $estadoKey }}
                                </div>
                            </div>
                        </div>

                        @if(!empty($seguimiento->observaciones))
                            <div class="mt-5 rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Observaciones</div>
                                <div class="mt-2 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">
                                    {{ $seguimiento->observaciones }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Update --}}
                    <div
                        x-data="{
                            incoterm: @js(old('incoterm', $seguimiento->incoterm ?? '')),
                            tipoEnvio: @js(old('tipo_envio', $seguimiento->tipo_envio ?? 'maritimo')),
                            incoterms: @js($incoterms),
                        }"
                        class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Actualizar seguimiento</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Cambia estado, fechas, incoterm y datos aéreos si aplica.
                                </div>
                            </div>
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold ring-1
                                         bg-gray-100 text-gray-900 dark:bg-gray-900/40 dark:text-gray-100">
                                #{{ $seguimiento->id }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('admin.seguimientos.update', $seguimiento->id) }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Proveedor (opcional)</label>
                                    <select name="proveedor_id" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">— Sin proveedor —</option>
                                        @foreach($proveedores as $p)
                                            <option value="{{ $p->id }}" {{ (string)old('proveedor_id', $seguimiento->proveedor_id) === (string)$p->id ? 'selected' : '' }}>
                                                {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">País destino</label>
                                    <input type="text" name="pais_destino" value="{{ old('pais_destino', $seguimiento->pais_destino ?? 'Colombia') }}"
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
                                                <div class="mt-1">Usa contenedores, BL, naviera y puertos.</div>
                                            </div>
                                        </template>
                                        <template x-if="tipoEnvio === 'aereo'">
                                            <div>
                                                <div class="font-extrabold">Aéreo</div>
                                                <div class="mt-1">Control por AWB, aerolínea, aeropuertos y vuelo.</div>
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
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                                    <select name="estado" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @foreach($estados as $k => $label)
                                            <option value="{{ $k }}" {{ old('estado', $seguimiento->estado) === $k ? 'selected' : '' }}>
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
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">ETD</label>
                                        <input type="date" name="etd" value="{{ old('etd', optional($seguimiento->etd)->format('Y-m-d')) }}"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">ETA</label>
                                        <input type="date" name="eta" value="{{ old('eta', optional($seguimiento->eta)->format('Y-m-d')) }}"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>
                                </div>
                            </div>

                            {{-- Detalles incoterm --}}
                            <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-base font-extrabold text-gray-900 dark:text-gray-100">Detalles del Incoterm</div>
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
                                                       value="{{ old('incoterm_detalles.lugar_retiro', $incotermDetalles['lugar_retiro'] ?? '') }}"
                                                       placeholder="Ej: Bodega Shenzhen / Dirección proveedor"
                                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                                    Obligatorio en EXW (dónde se recoge la carga).
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Contacto / referencia (opcional)</label>
                                                <input name="incoterm_detalles[contacto_retiro]"
                                                       value="{{ old('incoterm_detalles.contacto_retiro', $incotermDetalles['contacto_retiro'] ?? '') }}"
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
                                                       value="{{ old('incoterm_detalles.puerto_carga', $incotermDetalles['puerto_carga'] ?? '') }}"
                                                       placeholder="Ej: Puerto de Shanghái"
                                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                                    Obligatorio en FOB (dónde se embarca).
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Fecha listo en puerto (opcional)</label>
                                                <input type="date" name="incoterm_detalles[fecha_listo_puerto]"
                                                       value="{{ old('incoterm_detalles.fecha_listo_puerto', $incotermDetalles['fecha_listo_puerto'] ?? '') }}"
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
                                                       value="{{ old('incoterm_detalles.puerto_destino', $incotermDetalles['puerto_destino'] ?? '') }}"
                                                       placeholder="Ej: Cartagena / Buenaventura"
                                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                                    Obligatorio en CIF (hasta dónde cubre el vendedor).
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Aseguradora (opcional)</label>
                                                <input name="incoterm_detalles[aseguradora]"
                                                       value="{{ old('incoterm_detalles.aseguradora', $incotermDetalles['aseguradora'] ?? '') }}"
                                                       placeholder="Ej: Allianz / Mapfre"
                                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Valor seguro (opcional)</label>
                                                <input type="number" step="0.01" min="0" name="incoterm_detalles[valor_seguro]"
                                                       value="{{ old('incoterm_detalles.valor_seguro', $incotermDetalles['valor_seguro'] ?? '') }}"
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

                            {{-- AÉREO --}}
                            <div x-cloak x-show="tipoEnvio === 'aereo'" class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-base font-extrabold text-gray-900 dark:text-gray-100">Datos de envío AÉREO</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            AWB, aerolínea, aeropuertos y vuelo.
                                        </div>
                                    </div>
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 ring-1 ring-blue-100
                                                 dark:bg-blue-500/10 dark:text-blue-200 dark:ring-blue-500/20">
                                        AÉREO
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">AWB</label>
                                        <input name="awb" value="{{ old('awb', $seguimiento->awb) }}" placeholder="Ej: 123-45678901"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Aerolínea</label>
                                        <input name="aerolinea" value="{{ old('aerolinea', $seguimiento->aerolinea) }}" placeholder="Ej: Avianca Cargo"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Aeropuerto salida</label>
                                        <input name="aeropuerto_salida" value="{{ old('aeropuerto_salida', $seguimiento->aeropuerto_salida) }}" placeholder="Ej: PVG / Shanghai"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Aeropuerto llegada</label>
                                        <input name="aeropuerto_llegada" value="{{ old('aeropuerto_llegada', $seguimiento->aeropuerto_llegada) }}" placeholder="Ej: BOG / Bogotá"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Vuelo (opcional)</label>
                                        <input name="vuelo" value="{{ old('vuelo', $seguimiento->vuelo) }}" placeholder="Ej: AV1234"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Tracking URL (opcional)</label>
                                        <input name="tracking_url" value="{{ old('tracking_url', $seguimiento->tracking_url) }}" placeholder="https://..."
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>
                                </div>
                            </div>

                            {{-- Observaciones --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Observaciones</label>
                                <textarea name="observaciones" rows="4"
                                          class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 p-4"
                                          placeholder="Notas internas del proceso...">{{ old('observaciones', $seguimiento->observaciones) }}</textarea>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 justify-end">
                                <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                {{-- ==========================
                     COLUMNA DERECHA (Contenedores + Eventos)
                     ========================== --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Contenedores (solo maritimo) --}}
                    <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Contenedores</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Solo aplica para envío marítimo.
                                    </div>
                                </div>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold ring-1
                                             {{ $tipoEnvio === 'maritimo' ? 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20' : 'bg-gray-100 text-gray-700 ring-gray-200 dark:bg-gray-900/40 dark:text-gray-200 dark:ring-gray-700' }}">
                                    {{ $tipoEnvio === 'maritimo' ? 'HABILITADO' : 'NO APLICA' }}
                                </span>
                            </div>
                        </div>

                        @if($tipoEnvio !== 'maritimo')
                            <div class="p-6">
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                    Este seguimiento es <b>AÉREO</b>. No se usan contenedores.
                                </div>
                            </div>
                        @else
                            <div class="p-6 space-y-5">

                                {{-- Crear contenedor --}}
                                <form method="POST" action="{{ route('admin.seguimientos.contenedores.store', $seguimiento->id) }}" class="space-y-4">
                                    {{-- Ruta sugerida:
                                         Route::post('/admin/seguimientos/{seguimiento}/contenedores', [SeguimientoController::class,'contenedorStore'])->name('admin.seguimientos.contenedores.store');
                                     --}}
                                    @csrf

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Número contenedor</label>
                                            <input name="numero_contenedor" value="{{ old('numero_contenedor') }}" placeholder="Ej: MSKU1234567"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">BL</label>
                                            <input name="bl" value="{{ old('bl') }}" placeholder="Ej: BL-001234"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Naviera</label>
                                            <input name="naviera" value="{{ old('naviera') }}" placeholder="Ej: MSC / Maersk"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                                            <select name="estado"
                                                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                @foreach($estadosContenedor as $k => $label)
                                                    <option value="{{ $k }}" {{ old('estado','reservado') === $k ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Puerto salida</label>
                                            <input name="puerto_salida" value="{{ old('puerto_salida') }}" placeholder="Ej: Shanghái"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Puerto llegada</label>
                                            <input name="puerto_llegada" value="{{ old('puerto_llegada') }}" placeholder="Ej: Cartagena"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">ETD</label>
                                            <input type="date" name="etd" value="{{ old('etd') }}"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">ETA</label>
                                            <input type="date" name="eta" value="{{ old('eta') }}"
                                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        </div>
                                    </div>

                                    <button class="w-full px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                                        + Agregar contenedor
                                    </button>
                                </form>

                                {{-- Lista contenedores --}}
                                <div class="pt-2">
                                    <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100 mb-3">
                                        Registrados ({{ $contenedores->count() }})
                                    </div>

                                    @if($contenedores->isEmpty())
                                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                            Aún no hay contenedores asociados.
                                        </div>
                                    @else
                                        <div class="space-y-3">
                                            @foreach($contenedores as $c)
                                                <div x-data="{ open:false }" class="rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div>
                                                            <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                                                {{ $c->numero_contenedor ?? ('Contenedor #'.$c->id) }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                BL: {{ $c->bl ?? '—' }} · Naviera: {{ $c->naviera ?? '—' }}
                                                            </div>
                                                        </div>

                                                        <div class="text-right">
                                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold ring-1
                                                                bg-gray-100 text-gray-900 dark:bg-gray-900/40 dark:text-gray-100">
                                                                {{ $estadosContenedor[$c->estado] ?? $c->estado }}
                                                            </span>

                                                            <button type="button" @click="open = !open"
                                                                    class="mt-2 text-xs font-extrabold text-blue-600 hover:text-blue-700 dark:text-blue-300">
                                                                @{{ open ? 'Ocultar' : 'Editar' }}
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                                                        <div>ETD: <b class="text-gray-900 dark:text-gray-100">{{ $c->etd?->format('d/m/Y') ?? '—' }}</b></div>
                                                        <div>ETA: <b class="text-gray-900 dark:text-gray-100">{{ $c->eta?->format('d/m/Y') ?? '—' }}</b></div>
                                                        <div class="col-span-2">
                                                            Ruta: <b class="text-gray-900 dark:text-gray-100">{{ $c->puerto_salida ?? '—' }}</b> → <b class="text-gray-900 dark:text-gray-100">{{ $c->puerto_llegada ?? '—' }}</b>
                                                        </div>
                                                    </div>

                                                    {{-- Form editar --}}
                                                    <div x-cloak x-show="open" class="mt-4">
                                                        <form method="POST" action="{{ route('admin.seguimientos.contenedores.update', [$seguimiento->id, $c->id]) }}"
                                                              class="space-y-3">
                                                            {{-- Ruta sugerida:
                                                                 Route::put('/admin/seguimientos/{seguimiento}/contenedores/{contenedor}', [SeguimientoController::class,'contenedorUpdate'])->name('admin.seguimientos.contenedores.update');
                                                             --}}
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">Número</label>
                                                                    <input name="numero_contenedor" value="{{ old('numero_contenedor', $c->numero_contenedor) }}"
                                                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">BL</label>
                                                                    <input name="bl" value="{{ old('bl', $c->bl) }}"
                                                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">Naviera</label>
                                                                    <input name="naviera" value="{{ old('naviera', $c->naviera) }}"
                                                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                                                                    <select name="estado"
                                                                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                                        @foreach($estadosContenedor as $k => $label)
                                                                            <option value="{{ $k }}" {{ old('estado', $c->estado) === $k ? 'selected' : '' }}>{{ $label }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">Puerto salida</label>
                                                                    <input name="puerto_salida" value="{{ old('puerto_salida', $c->puerto_salida) }}"
                                                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">Puerto llegada</label>
                                                                    <input name="puerto_llegada" value="{{ old('puerto_llegada', $c->puerto_llegada) }}"
                                                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">ETD</label>
                                                                    <input type="date" name="etd" value="{{ old('etd', optional($c->etd)->format('Y-m-d')) }}"
                                                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">ETA</label>
                                                                    <input type="date" name="eta" value="{{ old('eta', optional($c->eta)->format('Y-m-d')) }}"
                                                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                                </div>
                                                            </div>

                                                            <div class="flex gap-2">
                                                                <button class="flex-1 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                                                    Guardar
                                                                </button>

                                                                <button type="button"
                                                                        @click="$dispatch('open-delete-contenedor', { id: '{{ $c->id }}' })"
                                                                        class="px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-extrabold
                                                                               dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-200">
                                                                    Eliminar
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endif
                    </div>

                    {{-- Eventos / Bitácora --}}
                    <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Bitácora</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Eventos del seguimiento (comentarios, documentos, hitos).
                            </div>
                        </div>

                        <div class="p-6 space-y-5">

                            {{-- Crear evento --}}
                            <form method="POST" action="{{ route('admin.seguimientos.eventos.store', $seguimiento->id) }}"
                                  enctype="multipart/form-data"
                                  class="space-y-4">
                                {{-- Rutas sugeridas:
                                     Route::post('/admin/seguimientos/{seguimiento}/eventos', [SeguimientoController::class,'eventoStore'])->name('admin.seguimientos.eventos.store');
                                     Route::delete('/admin/seguimientos/{seguimiento}/eventos/{evento}', [SeguimientoController::class,'eventoDestroy'])->name('admin.seguimientos.eventos.destroy');
                                 --}}
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Tipo</label>
                                        <select name="tipo"
                                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                            <option value="general" {{ old('tipo','general') === 'general' ? 'selected' : '' }}>General</option>
                                            <option value="embarque" {{ old('tipo') === 'embarque' ? 'selected' : '' }}>Embarque</option>
                                            <option value="aduana" {{ old('tipo') === 'aduana' ? 'selected' : '' }}>Aduana</option>
                                            <option value="documento" {{ old('tipo') === 'documento' ? 'selected' : '' }}>Documento</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Fecha (opcional)</label>
                                        <input type="date" name="fecha_evento" value="{{ old('fecha_evento') }}"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Título</label>
                                        <input name="titulo" value="{{ old('titulo') }}" placeholder="Ej: Documentos de aduana enviados"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Descripción (opcional)</label>
                                        <textarea name="descripcion" rows="3"
                                                  class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 p-4"
                                                  placeholder="Detalles...">{{ old('descripcion') }}</textarea>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                            Archivo (opcional) <span class="font-normal text-gray-500">pdf/jpg/png/webp (max 5MB)</span>
                                        </label>
                                        <input type="file" name="archivo"
                                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 p-2">
                                    </div>
                                </div>

                                <button class="w-full px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                    + Agregar evento
                                </button>
                            </form>

                            {{-- Listado de eventos --}}
                            <div class="pt-2">
                                <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100 mb-3">
                                    Historial ({{ $eventos->count() }})
                                </div>

                                @if($eventos->isEmpty())
                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                        Aún no hay eventos.
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach($eventos as $ev)
                                            @php
                                                $tipoPill = 'bg-gray-100 text-gray-900 dark:bg-gray-900/40 dark:text-gray-100';
                                                if (($ev->tipo ?? '') === 'aduana') $tipoPill = 'bg-yellow-50 text-yellow-800 dark:bg-yellow-500/10 dark:text-yellow-200';
                                                if (($ev->tipo ?? '') === 'embarque') $tipoPill = 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-200';
                                                if (($ev->tipo ?? '') === 'documento') $tipoPill = 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-200';
                                            @endphp

                                            <div class="rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                                            {{ $ev->titulo }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ optional($ev->fecha_evento)->format('d/m/Y H:i') ?? '—' }}
                                                            @if(!empty($ev->creador?->name)) · Por: {{ $ev->creador->name }} @endif
                                                        </div>
                                                    </div>

                                                    <div class="text-right">
                                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold {{ $tipoPill }}">
                                                            {{ strtoupper($ev->tipo ?? 'general') }}
                                                        </span>

                                                        <button type="button"
                                                                @click="$dispatch('open-delete-evento', { id: '{{ $ev->id }}' })"
                                                                class="mt-2 block text-xs font-extrabold text-red-600 hover:text-red-700 dark:text-red-300">
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </div>

                                                @if(!empty($ev->descripcion))
                                                    <div class="mt-3 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">
                                                        {{ $ev->descripcion }}
                                                    </div>
                                                @endif

                                                @if(!empty($ev->archivo))
                                                    <div class="mt-4">
                                                        <a href="{{ asset('storage/'.$ev->archivo) }}" target="_blank"
                                                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                                                                  dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                                                            Ver archivo
                                                        </a>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                            {{ basename($ev->archivo) }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            {{-- ==========================
                 MODALES DE ELIMINACIÓN (Contenedor / Evento)
                 ========================== --}}
            <div
                x-data="{
                    open: false,
                    type: null,
                    id: null,
                    openContenedor(id){ this.open=true; this.type='contenedor'; this.id=id; },
                    openEvento(id){ this.open=true; this.type='evento'; this.id=id; },
                    close(){ this.open=false; this.type=null; this.id=null; },
                }"
                @open-delete-contenedor.window="openContenedor($event.detail.id)"
                @open-delete-evento.window="openEvento($event.detail.id)"
                x-cloak
            >
                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50" @click="close()"></div>

                    <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                    Confirmar eliminación
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Esta acción no se puede deshacer.
                                </div>
                            </div>

                            <button type="button" @click="close()"
                                    class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-900/40 dark:hover:bg-gray-900/60">
                                ✕
                            </button>
                        </div>

                        <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/30 dark:text-red-100">
                            <div class="font-extrabold">¿Eliminar definitivamente?</div>
                            <div class="text-sm mt-1">
                                <span x-show="type==='contenedor'">Se eliminará el contenedor seleccionado.</span>
                                <span x-show="type==='evento'">Se eliminará el evento seleccionado (y su archivo si existe).</span>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col sm:flex-row gap-2 justify-end">
                            <button type="button" @click="close()"
                                    class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                                           dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100">
                                Cancelar
                            </button>

                            {{-- Formularios reales (se activan según type) --}}
                            <form x-show="type==='contenedor'"
                                  method="POST"
                                  :action="`{{ url('/admin/seguimientos/'.$seguimiento->id.'/contenedores') }}/${id}`">
                                @csrf
                                @method('DELETE')
                                <button class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold w-full">
                                    Sí, eliminar
                                </button>
                            </form>

                            <form x-show="type==='evento'"
                                  method="POST"
                                  :action="`{{ url('/admin/seguimientos/'.$seguimiento->id.'/eventos') }}/${id}`">
                                @csrf
                                @method('DELETE')
                                <button class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold w-full">
                                    Sí, eliminar
                                </button>
                            </form>
                        </div>

                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-4">
                            Si tus rutas tienen otro formato/nombre, ajusta los <code class="font-bold">url()</code> de estos forms.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
