{{-- resources/views/admin/seguimientos/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">Seguimientos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Control logístico de las ventas (fechas, estado, contenedores y bitácora).
                </p>
            </div>

            <a href="{{ route('admin.ventas.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                      dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                Ir a Ventas
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- Filtros --}}
            <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-5">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-5">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="q" value="{{ $q ?? '' }}"
                               placeholder="Venta #, nombre, email o empresa"
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                        <select name="estado"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Todos</option>
                            @foreach($estados as $k => $label)
                                <option value="{{ $k }}" {{ ($estado ?? '') === $k ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3 flex gap-2">
                        <button class="w-full px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.seguimientos.index') }}"
                           class="w-full px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                                  dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100 text-center">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="py-3 px-4 text-left font-extrabold text-gray-700 dark:text-gray-200">Venta</th>
                                <th class="py-3 px-4 text-left font-extrabold text-gray-700 dark:text-gray-200">Cliente</th>
                                <th class="py-3 px-4 text-left font-extrabold text-gray-700 dark:text-gray-200">Proveedor</th>
                                <th class="py-3 px-4 text-left font-extrabold text-gray-700 dark:text-gray-200">Estado</th>

                                {{--  PRO: Fechas en lenguaje humano --}}
                                <th class="py-3 px-4 text-left font-extrabold text-gray-700 dark:text-gray-200">
                                    Fechas (Salida / Llegada)
                                    <div class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">
                                        Salida estimada (ETD) · Llegada estimada (ETA)
                                    </div>
                                </th>

                                <th class="py-3 px-4 text-right font-extrabold text-gray-700 dark:text-gray-200">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($seguimientos as $s)
                                @php
                                    $venta = $s->venta;
                                    $cliente = $venta->usuario ?? null;

                                    //  PRO: faltan días (si hay ETA)
                                    $diasParaLlegar = null;
                                    if ($s->eta) {
                                        $diasParaLlegar = now()->startOfDay()->diffInDays($s->eta->startOfDay(), false);
                                    }
                                @endphp

                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="py-3 px-4">
                                        <div class="font-extrabold text-gray-900 dark:text-gray-100">
                                            #{{ $venta->id ?? '—' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Total: ${{ number_format((float)($venta->total_venta ?? 0), 2, ',', '.') }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <div class="font-bold text-gray-900 dark:text-gray-100">{{ $cliente->name ?? '—' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $cliente->empresa ?? '' }} {{ $cliente?->email ? '· '.$cliente->email : '' }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <div class="font-bold text-gray-900 dark:text-gray-100">{{ $s->proveedor->nombre ?? '—' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Destino: {{ $s->pais_destino ?? '—' }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold
                                            bg-blue-50 text-blue-700 ring-1 ring-blue-100
                                            dark:bg-blue-500/10 dark:text-blue-200 dark:ring-blue-500/20">
                                            {{ $estados[$s->estado] ?? $s->estado }}
                                        </span>

                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ strtoupper($s->tipo_envio ?? '—') }} {{ $s->incoterm ? '· '.$s->incoterm : '' }}
                                        </div>
                                    </td>

                                    {{--  PRO: Fechas claras --}}
                                    <td class="py-3 px-4">
                                        <div class="space-y-1">
                                            <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                                Salida: {{ $s->etd?->format('d/m/Y') ?? '—' }}
                                            </div>
                                            <div class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                                Llegada: {{ $s->eta?->format('d/m/Y') ?? '—' }}
                                            </div>
                                        </div>

                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            Actualizado: {{ $s->updated_at?->format('d/m/Y H:i') }}
                                        </div>

                                        {{--  PRO: faltan días --}}
                                        @if(!is_null($diasParaLlegar))
                                            <div class="mt-1 text-xs font-bold">
                                                @if($diasParaLlegar > 0)
                                                    <span class="text-gray-600 dark:text-gray-300">Faltan</span>
                                                    <span class="text-blue-600 dark:text-blue-300 font-extrabold">{{ $diasParaLlegar }}</span>
                                                    <span class="text-gray-600 dark:text-gray-300">día(s)</span>
                                                @elseif($diasParaLlegar === 0)
                                                    <span class="text-green-700 dark:text-green-200 font-extrabold">Llega hoy (según ETA)</span>
                                                @else
                                                    <span class="text-red-700 dark:text-red-200 font-extrabold">
                                                        ETA vencida hace {{ abs($diasParaLlegar) }} día(s)
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('admin.seguimientos.show', $s->id) }}"
                                           class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                                                  dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                                            Abrir
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 px-4 text-center text-gray-600 dark:text-gray-300">
                                        No hay seguimientos aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $seguimientos->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
