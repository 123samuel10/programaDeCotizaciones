{{-- resources/views/admin/cotizaciones/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4">
            {{-- Top line: breadcrumb + actions --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5 12 3l9 7.5V21a1.5 1.5 0 0 1-1.5 1.5H4.5A1.5 1.5 0 0 1 3 21V10.5Z"
                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 22v-7a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7"
                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Panel
                        </span>
                        <span class="opacity-60">/</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Cotizaciones</span>
                    </div>

                    <div class="mt-1 flex items-center gap-3 flex-wrap">
                        <h2 class="font-extrabold text-2xl text-gray-900 dark:text-gray-100 tracking-tight">
                            Cotizaciones
                        </h2>

                        <span class="text-[11px] font-extrabold px-2.5 py-1 rounded-full
                                     bg-gray-100 text-gray-700 ring-1 ring-gray-200
                                     dark:bg-gray-700/60 dark:text-gray-200 dark:ring-gray-600">
                            Total: {{ $cotizaciones->total() }}
                        </span>

                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Administra estados, revisa respuestas y controla trazabilidad.
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.cotizaciones.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold shadow-sm ring-1 ring-blue-500/20">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Nueva cotización
                    </a>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 text-green-900
                            dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-100 px-5 py-4">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 inline-flex w-9 h-9 rounded-xl items-center justify-center
                                     bg-green-100 text-green-700 ring-1 ring-green-200
                                     dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"
                                      stroke="currentColor" stroke-width="1.6" opacity=".6"/>
                            </svg>
                        </span>
                        <div>
                            <div class="font-extrabold">Operación exitosa</div>
                            <div class="text-sm mt-0.5">{{ session('success') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 text-red-900
                            dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-100 px-5 py-4">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 inline-flex w-9 h-9 rounded-xl items-center justify-center
                                     bg-red-100 text-red-700 ring-1 ring-red-200
                                     dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 9v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M10.3 3.7h3.4L22 18.5A2 2 0 0 1 20.3 21H3.7A2 2 0 0 1 2 18.5L10.3 3.7Z"
                                      stroke="currentColor" stroke-width="1.6" opacity=".6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <div class="font-extrabold">Ocurrió un problema</div>
                            <div class="text-sm mt-0.5">{{ session('error') }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Card principal --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

                {{-- Toolbar --}}
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                        {{-- Tabs por estado (frontend) --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button"
                                    class="js-tab px-3.5 py-2 rounded-xl text-sm font-extrabold
                                           bg-gray-900 text-white dark:bg-white dark:text-gray-900"
                                    data-filter="all">
                                Todos
                            </button>

                            <button type="button"
                                    class="js-tab px-3.5 py-2 rounded-xl text-sm font-extrabold
                                           bg-gray-100 hover:bg-gray-200 text-gray-800 ring-1 ring-gray-200
                                           dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100 dark:ring-gray-600"
                                    data-filter="pendiente">
                                Pendientes
                            </button>

                            <button type="button"
                                    class="js-tab px-3.5 py-2 rounded-xl text-sm font-extrabold
                                           bg-gray-100 hover:bg-gray-200 text-gray-800 ring-1 ring-gray-200
                                           dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100 dark:ring-gray-600"
                                    data-filter="aceptada">
                                Aceptadas
                            </button>

                            <button type="button"
                                    class="js-tab px-3.5 py-2 rounded-xl text-sm font-extrabold
                                           bg-gray-100 hover:bg-gray-200 text-gray-800 ring-1 ring-gray-200
                                           dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100 dark:ring-gray-600"
                                    data-filter="rechazada">
                                Rechazadas
                            </button>

                            <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                Tip: filtra por estado o usa el buscador.
                            </span>
                        </div>

                        {{-- Search + count --}}
                        <div class="w-full lg:w-[420px]">
                            <label class="sr-only" for="cotizacionSearch">Buscar cotizaciones</label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                         viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M9 3a6 6 0 104.472 10.03l2.249 2.249a.75.75 0 101.06-1.06l-2.249-2.249A6 6 0 009 3zm-4.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </div>

                                <input id="cotizacionSearch"
                                       type="text"
                                       placeholder="Buscar (cliente, email, empresa, teléfono, NIT, estado, #, total)..."
                                       class="w-full pl-10 pr-24 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-700
                                              bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                              placeholder:text-gray-400 dark:placeholder:text-gray-500
                                              focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">

                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-2">
                                    <span id="visibleCount"
                                          class="text-[11px] font-extrabold px-2.5 py-1 rounded-full
                                                 bg-gray-100 text-gray-700 ring-1 ring-gray-200
                                                 dark:bg-gray-700/60 dark:text-gray-200 dark:ring-gray-600">
                                        —
                                    </span>

                                    <span class="hidden sm:inline-flex text-[10px] font-bold px-2 py-1 rounded-lg
                                                 bg-gray-50 text-gray-500 ring-1 ring-gray-200
                                                 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700">
                                        ⌘K
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50/80 dark:bg-gray-900/30">
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="py-3 px-6 font-extrabold">#</th>
                            <th class="py-3 px-6 font-extrabold">Cliente</th>
                            <th class="py-3 px-6 font-extrabold">Estado</th>
                            <th class="py-3 px-6 font-extrabold">Respondida</th>
                            <th class="py-3 px-6 font-extrabold text-center">Líneas</th>
                            <th class="py-3 px-6 font-extrabold text-right">Total Venta</th>
                            <th class="py-3 px-6 font-extrabold text-right">Acciones</th>
                        </tr>
                        </thead>

                        <tbody id="cotizacionesTbody" class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($cotizaciones as $c)
                            @php
                                $estado = $c->estado ?? 'pendiente';

                                $badge = match($estado) {
                                    'aceptada' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                                    'rechazada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                                    default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                                };

                                $u = $c->usuario;

                                $clienteNombre   = $u->name ?? '—';
                                $clienteEmail    = $u->email ?? '';
                                $clienteEmpresa  = $u->empresa ?? null;
                                $clienteTelefono = $u->telefono ?? null;
                                $clienteNit      = $u->nit ?? null;

                                $clientePais     = $u->pais ?? null;
                                $clienteCiudad   = $u->ciudad ?? null;
                                $clienteDir      = $u->direccion ?? null;

                                $ubicacion = trim((($clienteCiudad ?? '') . (($clientePais ?? '') ? ', '.$clientePais : '')), ', ');

                                $respondidaTxt = $c->respondida_en
                                    ? \Carbon\Carbon::parse($c->respondida_en)->format('Y-m-d H:i')
                                    : '—';

                                $lineas = $c->items_count ?? 0;
                                $totalVenta = (float) ($c->total_venta ?? 0);

                                // ✅ Regla: si tiene venta, no se puede eliminar
                                $tieneVenta = !empty($c->tiene_venta);

                                // texto para búsqueda (frontend)
                                $searchText = strtolower(
                                    $c->id.' '.
                                    $clienteNombre.' '.
                                    $clienteEmail.' '.
                                    ($clienteEmpresa ?? '').' '.
                                    ($clienteTelefono ?? '').' '.
                                    ($clienteNit ?? '').' '.
                                    ($clientePais ?? '').' '.
                                    ($clienteCiudad ?? '').' '.
                                    ($clienteDir ?? '').' '.
                                    $estado.' '.
                                    $respondidaTxt.' '.
                                    $lineas.' '.
                                    number_format($totalVenta, 2, ',', '.')
                                );
                            @endphp

                            <tr class="align-top hover:bg-gray-50/60 dark:hover:bg-gray-900/30 transition"
                                data-row-search="{{ $searchText }}"
                                data-estado="{{ $estado }}">

                                <td class="py-4 px-6 font-extrabold text-gray-900 dark:text-gray-100">
                                    {{ $c->id }}
                                </td>

                                {{-- Cliente --}}
                                <td class="py-4 px-6">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-gray-900 dark:text-gray-100 font-extrabold truncate">
                                                {{ $clienteNombre }}
                                            </div>

                                            {{-- Empresa / Tipo + NIT (✅ nunca queda vacío) --}}
                                            <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
                                                <span class="font-bold">
                                                    {{ $clienteEmpresa ? 'Empresa:' : 'Tipo:' }}
                                                </span>
                                                {{ $clienteEmpresa ?: 'Cliente sin empresa' }}

                                                @if($clienteNit)
                                                    <span class="mx-1">•</span>
                                                    <span class="font-bold">NIT:</span> {{ $clienteNit }}
                                                @endif
                                            </div>

                                            {{-- Email --}}
                                            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 break-words">
                                                {{ $clienteEmail }}
                                            </div>

                                            {{-- Teléfono + Dirección/Ubicación --}}
                                            @if($clienteTelefono || $clienteDir || $ubicacion)
                                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                                    @if($clienteTelefono)
                                                        <span class="inline-flex items-center gap-1">
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M6.5 3.5h2l1 4-2 1c1.2 2.4 3.1 4.3 5.5 5.5l1.1-2 4 1v2c0 1.1-.9 2-2 2C10.4 18 6 13.6 4 7.9c-.3-1 .5-2 2-2.4Z"
                                                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                            <span class="font-bold">Tel:</span> {{ $clienteTelefono }}
                                                        </span>
                                                    @endif

                                                    @if($clienteDir)
                                                        <span class="inline-flex items-center gap-1">
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M12 21s7-4.4 7-11a7 7 0 10-14 0c0 6.6 7 11 7 11Z"
                                                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M12 10.5a2.2 2.2 0 110-4.4 2.2 2.2 0 010 4.4Z"
                                                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                            <span class="font-bold">Dir:</span> {{ $clienteDir }}
                                                        </span>
                                                    @endif

                                                    @if($ubicacion)
                                                        <span class="inline-flex items-center gap-1">
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M20 12a8 8 0 11-16 0 8 8 0 0116 0Z"
                                                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M12 4v16M4 12h16"
                                                                      stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".55"/>
                                                            </svg>
                                                            {{ $ubicacion }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($c->nota_cliente)
                                                <div class="mt-2 text-xs p-2.5 rounded-2xl bg-gray-50 dark:bg-gray-900/30 border dark:border-gray-700 text-gray-700 dark:text-gray-200">
                                                    <span class="font-extrabold">Nota:</span> {{ $c->nota_cliente }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Badge Empresa / Cliente natural (✅ siempre aparece) --}}
                                        <span class="shrink-0 text-[11px] font-extrabold px-2.5 py-1 rounded-full ring-1
                                            {{ $clienteEmpresa
                                                ? 'bg-blue-50 text-blue-700 ring-blue-100 dark:bg-blue-500/10 dark:text-blue-200 dark:ring-blue-500/20'
                                                : 'bg-gray-100 text-gray-600 ring-gray-200 dark:bg-gray-700/60 dark:text-gray-300 dark:ring-gray-600'
                                            }}">
                                            {{ $clienteEmpresa ? 'Empresa' : 'Cliente natural' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Estado --}}
                                <td class="py-4 px-6">
                                    <span class="text-[11px] font-extrabold px-3 py-1 rounded-full ring-1 inline-flex {{ $badge }}">
                                        {{ strtoupper($estado) }}
                                    </span>

                                    {{-- ✅ Etiqueta PRO si ya tiene venta --}}
                                    @if($tieneVenta)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-extrabold px-2.5 py-1 rounded-full
                                                bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100
                                                dark:bg-emerald-500/10 dark:text-emerald-200 dark:ring-emerald-500/20">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M7 22h10a2 2 0 002-2V10a2 2 0 00-2-2h-2l-1-2h-4l-1 2H7a2 2 0 00-2 2v10a2 2 0 002 2Z"
                                                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                Convertida a venta
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Respondida --}}
                                <td class="py-4 px-6 text-gray-700 dark:text-gray-200">
                                    {{ $respondidaTxt }}
                                </td>

                                {{-- líneas --}}
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex min-w-[2.5rem] justify-center text-xs font-extrabold px-2.5 py-1 rounded-full
                                                 bg-gray-100 text-gray-700 ring-1 ring-gray-200
                                                 dark:bg-gray-700/60 dark:text-gray-200 dark:ring-gray-600">
                                        {{ $lineas }}
                                    </span>
                                </td>

                                {{-- total --}}
                                <td class="py-4 px-6 text-right text-gray-900 dark:text-gray-100 font-extrabold">
                                    ${{ number_format($totalVenta, 2, ',', '.') }}
                                </td>

                                {{-- acciones --}}
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.cotizaciones.edit', $c->id) }}"
                                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                                  bg-blue-50 hover:bg-blue-100 text-blue-700 font-extrabold ring-1 ring-blue-100
                                                  dark:bg-blue-500/10 dark:hover:bg-blue-500/15 dark:text-blue-200 dark:ring-blue-500/20">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"
                                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Abrir
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.cotizaciones.destroy', $c->id) }}"
                                              class="inline js-delete-quote-form"
                                              data-id="{{ $c->id }}"
                                              data-cliente="{{ $clienteNombre }}"
                                              data-email="{{ $clienteEmail }}"
                                              data-lineas="{{ $lineas }}"
                                              data-empresa="{{ $clienteEmpresa ?? '' }}"
                                              data-telefono="{{ $clienteTelefono ?? '' }}"
                                              data-nit="{{ $clienteNit ?? '' }}"
                                              data-tiene-venta="{{ $tieneVenta ? 1 : 0 }}">
                                            @csrf
                                            @method('DELETE')

                                            @if($tieneVenta)
                                                <button type="button"
                                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                                               bg-gray-100 text-gray-400 font-extrabold ring-1 ring-gray-200 cursor-not-allowed
                                                               dark:bg-gray-700/60 dark:text-gray-400 dark:ring-gray-600"
                                                        title="No se puede eliminar: ya tiene una venta asociada"
                                                        disabled>
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M12 17v.01M12 7v7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                                        <path d="M7 21h10a2 2 0 002-2v-7a2 2 0 00-2-2h-1V8a4 4 0 10-8 0v2H7a2 2 0 00-2 2v7a2 2 0 002 2Z"
                                                              stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    Bloqueado
                                                </button>
                                            @else
                                                <button type="button"
                                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                                               bg-red-50 hover:bg-red-100 text-red-700 font-extrabold ring-1 ring-red-100
                                                               dark:bg-red-500/10 dark:hover:bg-red-500/15 dark:text-red-200 dark:ring-red-500/20
                                                               js-open-delete-quote">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        <path d="M6 7l1 14h10l1-14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M9 7V4h6v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="mx-auto max-w-md">
                                        <div class="mx-auto w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-500 dark:text-gray-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M7 7h10M7 12h10M7 17h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"
                                                      stroke="currentColor" stroke-width="1.6" opacity=".6"/>
                                            </svg>
                                        </div>
                                        <div class="mt-3 font-extrabold text-gray-900 dark:text-gray-100">No hay cotizaciones aún</div>
                                        <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Crea una cotización para empezar a gestionar tu flujo comercial.
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ route('admin.cotizaciones.create') }}"
                                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                                Nueva cotización
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer: paginación --}}
                <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Mostrando <span class="font-extrabold">{{ $cotizaciones->firstItem() ?? 0 }}</span>
                        a <span class="font-extrabold">{{ $cotizaciones->lastItem() ?? 0 }}</span>
                        de <span class="font-extrabold">{{ $cotizaciones->total() }}</span>
                    </p>

                    <div>
                        {{ $cotizaciones->links() }}
                    </div>
                </div>

                <div class="px-6 pb-6 text-xs text-gray-500 dark:text-gray-400">
                    Consejo: usa estados (pendiente/aceptada/rechazada) para control rápido del CRM.
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ELIMINAR COTIZACIÓN --}}
    <div id="deleteQuoteModal"
         class="fixed inset-0 z-50 hidden"
         aria-labelledby="deleteQuoteModalTitle"
         aria-modal="true"
         role="dialog">
        <div class="absolute inset-0 bg-black/45 backdrop-blur-sm" id="deleteQuoteBackdrop"></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-3xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-2xl overflow-hidden
                        transform transition-all duration-200 scale-95 opacity-0"
                 id="deleteQuotePanel">

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center ring-1 ring-red-100 dark:ring-red-500/20">
                            <svg class="w-7 h-7 text-red-600 dark:text-red-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 9v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M10.3 3.7h3.4L22 18.5A2 2 0 0 1 20.3 21H3.7A2 2 0 0 1 2 18.5L10.3 3.7Z"
                                      stroke="currentColor" stroke-width="1.6" opacity=".6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <div class="flex-1">
                            <h3 id="deleteQuoteModalTitle" class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                Confirmar eliminación
                            </h3>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Vas a eliminar la cotización <span class="font-extrabold text-gray-900 dark:text-gray-100" id="dqId">#—</span>.
                                Esta acción <span class="font-extrabold">no se puede deshacer</span>.
                            </p>

                            <div class="mt-4 text-xs rounded-2xl bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700 p-3.5 space-y-1">
                                <div class="font-extrabold text-gray-800 dark:text-gray-100" id="dqCliente">—</div>
                                <div class="text-gray-500 dark:text-gray-300" id="dqEmail">—</div>

                                <div class="text-gray-500 dark:text-gray-300 hidden" id="dqEmpresaWrap">
                                    <span class="font-extrabold text-gray-700 dark:text-gray-200">Empresa:</span>
                                    <span id="dqEmpresa">—</span>
                                </div>

                                <div class="text-gray-500 dark:text-gray-300 hidden" id="dqTelefonoWrap">
                                    <span class="font-extrabold text-gray-700 dark:text-gray-200">Tel:</span>
                                    <span id="dqTelefono">—</span>
                                </div>

                                <div class="text-gray-500 dark:text-gray-300 hidden" id="dqNitWrap">
                                    <span class="font-extrabold text-gray-700 dark:text-gray-200">NIT:</span>
                                    <span id="dqNit">—</span>
                                </div>

                                <div class="text-gray-500 dark:text-gray-300 pt-1">
                                    Líneas: <span class="font-extrabold text-gray-700 dark:text-gray-200" id="dqLineas">—</span>
                                </div>
                            </div>

                            {{-- Bloqueo por venta --}}
                            <div id="dqVentaBlock"
                                 class="mt-4 hidden text-sm rounded-2xl border border-amber-200 bg-amber-50 text-amber-900
                                        dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-100 p-3.5">
                                <div class="font-extrabold">No se puede eliminar</div>
                                <div class="mt-1 text-xs">
                                    Esta cotización está <span class="font-extrabold">asociada a una venta</span>.
                                    Para mantener trazabilidad, solo puedes <span class="font-extrabold">consultarla</span>.
                                </div>
                            </div>

                            <p id="dqHint" class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                                Se eliminarán también todas las líneas y adiciones asociadas.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-6 flex gap-3">
                    <button type="button"
                            class="w-full px-4 py-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200
                                   dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-extrabold"
                            id="dqCancel">
                        Cancelar
                    </button>

                    <button type="button"
                            class="w-full px-4 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold
                                   disabled:opacity-60 disabled:cursor-not-allowed"
                            id="dqConfirm">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS (buscar + tabs + modal) --}}
    <script>
        (function () {
            // ===== Utils =====
            const $ = (s, r = document) => r.querySelector(s);
            const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
            const normalize = v => (v || '').toString().toLowerCase().trim();

            // ===== Dataset rows =====
            const tbody = $('#cotizacionesTbody');
            const rows = $$('tr[data-row-search]', tbody);
            const search = $('#cotizacionSearch');
            const visibleCount = $('#visibleCount');

            // tabs state
            const tabs = $$('.js-tab');
            let estadoActivo = 'all';

            function setActiveTab(btn) {
                tabs.forEach(t => {
                    t.classList.remove('bg-gray-900','text-white','dark:bg-white','dark:text-gray-900');
                    t.classList.add('bg-gray-100','hover:bg-gray-200','text-gray-800','ring-1','ring-gray-200','dark:bg-gray-700/60','dark:hover:bg-gray-700','dark:text-gray-100','dark:ring-gray-600');
                });

                btn.classList.add('bg-gray-900','text-white','dark:bg-white','dark:text-gray-900');
                btn.classList.remove('bg-gray-100','hover:bg-gray-200','text-gray-800','ring-1','ring-gray-200','dark:bg-gray-700/60','dark:hover:bg-gray-700','dark:text-gray-100','dark:ring-gray-600');
            }

            function applyFilters() {
                const q = normalize(search?.value);
                let visibles = 0;

                rows.forEach(r => {
                    const hay = r.getAttribute('data-row-search') || '';
                    const est = (r.getAttribute('data-estado') || 'pendiente').toLowerCase();

                    const matchText = !q || hay.includes(q);
                    const matchEstado = (estadoActivo === 'all') || (est === estadoActivo);

                    const show = matchText && matchEstado;
                    r.classList.toggle('hidden', !show);
                    if (show) visibles++;
                });

                if (visibleCount) {
                    visibleCount.textContent = `${visibles} / ${rows.length}`;
                }
            }

            // init visible count
            applyFilters();

            // Search listener
            if (search) {
                search.addEventListener('input', applyFilters);

                // ⌘K / Ctrl+K focus
                document.addEventListener('keydown', (e) => {
                    const isK = (e.key || '').toLowerCase() === 'k';
                    if (isK && (e.metaKey || e.ctrlKey)) {
                        e.preventDefault();
                        search.focus();
                        search.select();
                    }
                });
            }

            // Tabs listener
            tabs.forEach(btn => {
                btn.addEventListener('click', () => {
                    estadoActivo = btn.dataset.filter || 'all';
                    setActiveTab(btn);
                    applyFilters();
                });
            });

            // ===== Modal eliminar =====
            const modal = $('#deleteQuoteModal');
            const panel = $('#deleteQuotePanel');
            const backdrop = $('#deleteQuoteBackdrop');

            const dqId = $('#dqId');
            const dqCliente = $('#dqCliente');
            const dqEmail = $('#dqEmail');
            const dqLineas = $('#dqLineas');

            const dqEmpresaWrap = $('#dqEmpresaWrap');
            const dqTelefonoWrap = $('#dqTelefonoWrap');
            const dqNitWrap = $('#dqNitWrap');

            const dqEmpresa = $('#dqEmpresa');
            const dqTelefono = $('#dqTelefono');
            const dqNit = $('#dqNit');

            const dqVentaBlock = $('#dqVentaBlock');
            const dqHint = $('#dqHint');

            const cancelBtn = $('#dqCancel');
            const confirmBtn = $('#dqConfirm');

            let currentForm = null;

            function openModal(form) {
                currentForm = form;

                dqId.textContent = '#' + (form.dataset.id || '—');
                dqCliente.textContent = form.dataset.cliente || '—';
                dqEmail.textContent = form.dataset.email || '';
                dqLineas.textContent = form.dataset.lineas || '0';

                const empresa = (form.dataset.empresa || '').trim();
                const telefono = (form.dataset.telefono || '').trim();
                const nit = (form.dataset.nit || '').trim();

                dqEmpresaWrap.classList.toggle('hidden', !empresa);
                dqTelefonoWrap.classList.toggle('hidden', !telefono);
                dqNitWrap.classList.toggle('hidden', !nit);

                dqEmpresa.textContent = empresa || '—';
                dqTelefono.textContent = telefono || '—';
                dqNit.textContent = nit || '—';

                const tieneVenta = (form.dataset.tieneVenta || '0') === '1';

                dqVentaBlock.classList.toggle('hidden', !tieneVenta);

                if (tieneVenta) {
                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'No se puede eliminar';
                    dqHint.classList.add('hidden');
                } else {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Sí, eliminar';
                    dqHint.classList.remove('hidden');
                }

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                requestAnimationFrame(() => {
                    panel.classList.remove('scale-95', 'opacity-0');
                    panel.classList.add('scale-100', 'opacity-100');
                });

                cancelBtn.focus();
            }

            function closeModal() {
                panel.classList.remove('scale-100', 'opacity-100');
                panel.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    currentForm = null;
                }, 170);
            }

            $$('.js-open-delete-quote').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const form = e.currentTarget.closest('form');
                    openModal(form);
                });
            });

            cancelBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', closeModal);

            confirmBtn.addEventListener('click', () => {
                if (!currentForm) return;
                if (confirmBtn.disabled) return;

                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Eliminando...';
                currentForm.submit();
            });

            document.addEventListener('keydown', (e) => {
                if (modal.classList.contains('hidden')) return;
                if (e.key === 'Escape') closeModal();
            });
        })();
    </script>
</x-app-layout>
