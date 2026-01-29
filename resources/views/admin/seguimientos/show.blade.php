{{-- resources/views/admin/seguimientos/show.blade.php --}}
<x-app-layout>
    @php
        /**
         * ==========================
         *  Helpers de vista (PRO)
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

        $estadoLabel = $estados[$estadoKey] ?? $estadoKey;

        $badgeEstado = match(true) {
            in_array($estadoKey, ['entregado','cerrado']) => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
            in_array($estadoKey, ['aduana','arriboapuerto']) => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
            default => 'bg-blue-50 text-blue-700 ring-blue-100 dark:bg-blue-500/10 dark:text-blue-200 dark:ring-blue-500/20',
        };

        // Cliente
        $cliente = $seguimiento->venta->usuario ?? null;

        // Fechas formateadas
        $etdFmt = $seguimiento->etd?->format('d/m/Y') ?? '—';
        $etaFmt = $seguimiento->eta?->format('d/m/Y') ?? '—';
        $updFmt = $seguimiento->updated_at?->format('d/m/Y H:i') ?? '—';

        // Etiquetas cortas para "ejecutivo"
        $proveedorNombre = $seguimiento->proveedor->nombre ?? '—';
        $destino = $seguimiento->pais_destino ?? '—';
        $tipoEnvio = strtoupper($seguimiento->tipo_envio ?? '—');
        $incoterm = $seguimiento->incoterm ?? null;

        // Mini-descripciones
        $helpETD = 'ETD: Fecha estimada de salida desde el origen.';
        $helpETA = 'ETA: Fecha estimada de llegada al destino.';
        $helpIncoterm = 'Incoterm: regla comercial que define quién asume costos/riesgos (ej: FOB, CIF, EXW).';
        $helpBL = 'BL (Bill of Lading): documento de embarque, como “guía” del envío.';
    @endphp

    {{-- ==========================
         HEADER (más ejecutivo)
         ========================== --}}
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                        Seguimiento #{{ $seguimiento->id }}
                    </h2>

                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold ring-1 {{ $badgeEstado }}">
                        {{ $estadoLabel }}
                    </span>

                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                        Venta #{{ $seguimiento->venta->id ?? '—' }}
                    </span>
                </div>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">
                    Cliente:
                    <span class="font-extrabold text-gray-900 dark:text-gray-100">{{ $cliente->name ?? '—' }}</span>
                    @if(!empty($cliente?->empresa)) · <span class="font-semibold">{{ $cliente->empresa }}</span> @endif
                    @if(!empty($cliente?->email)) · <span class="font-semibold">{{ $cliente->email }}</span> @endif
                </p>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-bold" title="{{ $helpETD }}">ETD</span>: salida estimada ·
                    <span class="font-bold" title="{{ $helpETA }}">ETA</span>: llegada estimada
                    @if($incoterm)
                        · <span class="font-bold" title="{{ $helpIncoterm }}">Incoterm</span>: {{ $incoterm }}
                    @endif
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.seguimientos.index') }}"
                   class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                          dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100">
                    Volver
                </a>

                <a href="{{ route('admin.ventas.show', $seguimiento->venta->id) }}"
                   class="px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                          dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                    Ver venta
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ALERTAS --}}
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

            {{-- ==========================
                 PANEL RESUMEN (para jefe)
                 ========================== --}}
            <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- Resumen datos --}}
                    <div class="lg:col-span-7 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Proveedor</div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100 truncate">
                                    {{ $proveedorNombre }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Destino</div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100 truncate">
                                    {{ $destino }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Tipo envío</div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                    {{ $tipoEnvio }}
                                </div>
                                @if($incoterm)
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400" title="{{ $helpIncoterm }}">
                                        Incoterm: <span class="font-bold">{{ $incoterm }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400" title="{{ $helpETD }}">Salida estimada (ETD)</div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">{{ $etdFmt }}</div>
                            </div>

                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400" title="{{ $helpETA }}">Llegada estimada (ETA)</div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">{{ $etaFmt }}</div>
                            </div>

                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Última actualización</div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">{{ $updFmt }}</div>
                            </div>
                        </div>

                        @if($seguimiento->observaciones)
                            <div class="rounded-2xl bg-gray-50 border border-gray-200 text-gray-700
                                        dark:bg-gray-900/30 dark:border-gray-700 dark:text-gray-200 p-4">
                                <div class="font-extrabold mb-1">Observaciones internas</div>
                                <div class="text-sm whitespace-pre-line">{{ $seguimiento->observaciones }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- Progreso --}}
                    <div class="lg:col-span-5">
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-5 h-full">
                            <div class="flex items-end justify-between">
                                <div>
                                    <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Avance del proceso</div>
                                    <div class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $progreso }}%</div>
                                </div>

                                @if(!is_null($diasParaLlegar))
                                    <div class="text-right text-sm font-bold">
                                        @if($diasParaLlegar > 0)
                                            <span class="text-gray-600 dark:text-gray-300">Faltan</span>
                                            <span class="text-blue-600 dark:text-blue-300 font-extrabold">{{ $diasParaLlegar }}</span>
                                            <span class="text-gray-600 dark:text-gray-300">día(s)</span>
                                        @elseif($diasParaLlegar === 0)
                                            <span class="text-green-700 dark:text-green-200 font-extrabold">Llega hoy</span>
                                        @else
                                            <span class="text-red-700 dark:text-red-200 font-extrabold">
                                                ETA vencida ({{ abs($diasParaLlegar) }} día[s])
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 w-full rounded-full bg-gray-100 dark:bg-gray-900/40 overflow-hidden">
                                <div class="h-3 rounded-full bg-blue-600" style="width: {{ $progreso }}%"></div>
                            </div>

                            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                                Estado actual: <span class="font-extrabold text-gray-900 dark:text-gray-100">{{ $estadoLabel }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ==========================
                 CUERPO: editor + contenedores + bitácora
                 ========================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- PANEL IZQUIERDO --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Editar seguimiento --}}
                    <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Editar seguimiento</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Actualiza datos generales del envío.</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.seguimientos.update', $seguimiento->id) }}" class="mt-5 space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Proveedor</label>
                                <select name="proveedor_id" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    <option value="">— Sin proveedor —</option>
                                    @foreach($proveedores as $p)
                                        <option value="{{ $p->id }}" {{ (old('proveedor_id', $seguimiento->proveedor_id) == $p->id) ? 'selected' : '' }}>
                                            {{ $p->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">País destino</label>
                                    <input type="text" name="pais_destino" value="{{ old('pais_destino', $seguimiento->pais_destino) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Tipo envío</label>
                                    <select name="tipo_envio" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="maritimo" {{ old('tipo_envio', $seguimiento->tipo_envio) === 'maritimo' ? 'selected' : '' }}>Marítimo</option>
                                        <option value="aereo" {{ old('tipo_envio', $seguimiento->tipo_envio) === 'aereo' ? 'selected' : '' }}>Aéreo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                        Incoterm <span class="font-normal text-gray-500" title="{{ $helpIncoterm }}">(¿qué es?)</span>
                                    </label>
                                    <input type="text" name="incoterm" value="{{ old('incoterm', $seguimiento->incoterm) }}"
                                           placeholder="FOB / CIF / EXW..."
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
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
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1" title="{{ $helpETD }}">
                                        Salida estimada (ETD)
                                    </label>
                                    <input type="date" name="etd" value="{{ old('etd', optional($seguimiento->etd)->format('Y-m-d')) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1" title="{{ $helpETA }}">
                                        Llegada estimada (ETA)
                                    </label>
                                    <input type="date" name="eta" value="{{ old('eta', optional($seguimiento->eta)->format('Y-m-d')) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Observaciones</label>
                                <textarea name="observaciones" rows="4"
                                          class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 p-4"
                                          placeholder="Notas internas...">{{ old('observaciones', $seguimiento->observaciones) }}</textarea>
                            </div>

                            <button class="w-full px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                Guardar cambios
                            </button>
                        </form>
                    </div>

{{-- ==========================
     CONTENEDORES (LISTA + MODAL EDITAR)
     ========================== --}}
<div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
    <div class="flex items-start justify-between gap-3">
        <div>
            <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Contenedores</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Registra y actualiza BL, naviera, puertos y estado.
                <span class="text-xs" title="{{ $helpBL }}">(BL = documento de embarque)</span>
            </div>
        </div>
    </div>

    {{-- FORM AGREGAR CONTENEDOR --}}
    <form method="POST"
          action="{{ route('admin.seguimientos.contenedores.store', $seguimiento->id) }}"
          class="mt-5 space-y-3">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Número contenedor</label>
                <input name="numero_contenedor"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                       placeholder="ABCD1234567">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1" title="{{ $helpBL }}">BL</label>
                <input name="bl"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                       placeholder="Bill of Lading">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Naviera</label>
                <input name="naviera"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                       placeholder="COSCO / MAERSK">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                <select name="estado"
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    @foreach($estadosContenedor as $k => $label)
                        <option value="{{ $k }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Puerto salida</label>
                <input name="puerto_salida"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Puerto llegada</label>
                <input name="puerto_llegada"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1" title="{{ $helpETD }}">ETD</label>
                <input type="date" name="etd"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1" title="{{ $helpETA }}">ETA</label>
                <input type="date" name="eta"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>
        </div>

        <button class="w-full px-5 py-2.5 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                       dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
            Agregar contenedor
        </button>
    </form>

    {{-- LISTA CONTENEDORES --}}
    @if($seguimiento->contenedores->isNotEmpty())
        <div class="mt-6 space-y-4">
            @foreach($seguimiento->contenedores as $c)
                @php
                    $modalId = 'modal-editar-contenedor-'.$c->id;
                    $estadoCont = $estadosContenedor[$c->estado] ?? $c->estado ?? '—';
                @endphp

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="font-extrabold text-gray-900 dark:text-gray-100 truncate">
                                {{ $c->numero_contenedor ?: 'Contenedor #'.$c->id }}
                                @if($c->bl)
                                    <span class="text-sm text-gray-500 dark:text-gray-400" title="{{ $helpBL }}">· BL: {{ $c->bl }}</span>
                                @endif
                            </div>

                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-bold">Naviera:</span> {{ $c->naviera ?? '—' }}
                                · <span class="font-bold">Estado:</span> {{ $estadoCont }}
                            </div>

                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-bold">Ruta:</span> {{ $c->puerto_salida ?? '—' }} → {{ $c->puerto_llegada ?? '—' }}
                            </div>

                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-bold">ETD:</span> {{ $c->etd?->format('d/m/Y') ?? '—' }}
                                · <span class="font-bold">ETA:</span> {{ $c->eta?->format('d/m/Y') ?? '—' }}
                            </div>
                        </div>

                        <div class="flex gap-2 shrink-0">
                            {{-- BOTÓN EDITAR (ABRE MODAL) --}}
                            <button type="button"
                                    onclick="document.getElementById('{{ $modalId }}').classList.remove('hidden')"
                                    class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                Editar
                            </button>

                            {{-- ELIMINAR (FORM SEPARADO, SIN ANIDAR) --}}
                            <form method="POST"
                                  action="{{ route('admin.seguimientos.contenedores.destroy', [$seguimiento->id, $c->id]) }}"
                                  onsubmit="return confirm('¿Eliminar este contenedor?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ==========================
                     MODAL EDITAR CONTENEDOR
                     ========================== --}}
                <div id="{{ $modalId }}" class="hidden fixed inset-0 z-50">
                    {{-- Fondo --}}
                    <div class="absolute inset-0 bg-black/50"
                         onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"></div>

                    {{-- Caja --}}
                    <div class="relative mx-auto mt-24 w-[95%] max-w-3xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl">
                        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Editar contenedor</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $c->numero_contenedor ?: 'Contenedor #'.$c->id }}
                                </div>
                            </div>

                            <button type="button"
                                    onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"
                                    class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                                           dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100">
                                Cerrar
                            </button>
                        </div>

                        <form method="POST"
                              action="{{ route('admin.seguimientos.contenedores.update', [$seguimiento->id, $c->id]) }}"
                              class="p-5 space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Número contenedor</label>
                                    <input name="numero_contenedor" value="{{ old('numero_contenedor', $c->numero_contenedor) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1" title="{{ $helpBL }}">BL</label>
                                    <input name="bl" value="{{ old('bl', $c->bl) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Naviera</label>
                                    <input name="naviera" value="{{ old('naviera', $c->naviera) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                                    <select name="estado"
                                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                        @foreach($estadosContenedor as $k => $label)
                                            <option value="{{ $k }}" {{ old('estado', $c->estado) === $k ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Puerto salida</label>
                                    <input name="puerto_salida" value="{{ old('puerto_salida', $c->puerto_salida) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Puerto llegada</label>
                                    <input name="puerto_llegada" value="{{ old('puerto_llegada', $c->puerto_llegada) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1" title="{{ $helpETD }}">ETD</label>
                                    <input type="date" name="etd"
                                           value="{{ old('etd', optional($c->etd)->format('Y-m-d')) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1" title="{{ $helpETA }}">ETA</label>
                                    <input type="date" name="eta"
                                           value="{{ old('eta', optional($c->eta)->format('Y-m-d')) }}"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-100">
                                </div>
                            </div>

                            <div class="flex gap-2 pt-2">
                                <button class="flex-1 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                    Guardar cambios
                                </button>

                                <button type="button"
                                        onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"
                                        class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                                               dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="mt-5 text-sm text-gray-600 dark:text-gray-300">
            Aún no hay contenedores registrados.
        </div>
    @endif
</div>


                </div>

                {{-- PANEL DERECHO --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Form evento --}}
                    <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">Bitácora (Eventos)</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Registro interno: qué pasó y cuándo.</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.seguimientos.eventos.store', $seguimiento->id) }}"
                              enctype="multipart/form-data"
                              class="mt-5 space-y-3">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Tipo</label>
                                    <select name="tipo" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="general">General</option>
                                        <option value="produccion">Producción</option>
                                        <option value="embarque">Embarque</option>
                                        <option value="aduana">Aduana</option>
                                        <option value="entrega">Entrega</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Título</label>
                                    <input name="titulo" required
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                           placeholder="Ej: Proveedor confirma fecha de fabricación">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Descripción (opcional)</label>
                                <textarea name="descripcion" rows="3"
                                          class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 p-4"
                                          placeholder="Detalles..."></textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Fecha evento (opcional)</label>
                                    <input type="datetime-local" name="fecha_evento"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Si lo dejas vacío, se usa la fecha actual.</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Archivo (opcional)</label>
                                    <input type="file" name="archivo"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">pdf/jpg/png/webp (máx 5MB)</p>
                                </div>
                            </div>

                            <button class="w-full px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                Agregar evento
                            </button>
                        </form>
                    </div>

                    {{-- Lista eventos --}}
                    <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
                        <div class="space-y-4">
                            @forelse($seguimiento->eventos as $e)
                                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                                {{ $e->titulo }}
                                            </div>

                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ strtoupper($e->tipo ?? 'GENERAL') }}
                                                · {{ $e->fecha_evento?->format('d/m/Y H:i') ?? $e->created_at?->format('d/m/Y H:i') }}
                                                @if($e->creador)
                                                    · Por: {{ $e->creador->name }}
                                                @endif
                                            </div>

                                            @if($e->descripcion)
                                                <div class="text-sm text-gray-700 dark:text-gray-200 mt-2 whitespace-pre-line">
                                                    {{ $e->descripcion }}
                                                </div>
                                            @endif

                                            @if($e->archivo)
                                                <div class="mt-2">
                                                    <a href="{{ asset('storage/'.$e->archivo) }}" target="_blank"
                                                       class="inline-flex items-center gap-2 text-blue-600 hover:underline font-extrabold">
                                                        Ver archivo adjunto
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('admin.seguimientos.eventos.destroy', [$seguimiento->id, $e->id]) }}"
                                              onsubmit="return confirm('¿Eliminar este evento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-3 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-gray-600 dark:text-gray-300">
                                    Aún no hay eventos. Agrega el primero arriba.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
