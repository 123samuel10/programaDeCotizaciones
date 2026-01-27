{{-- resources/views/admin/ventas/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white leading-tight">
                    Ventas
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ventas generadas automáticamente cuando el cliente acepta una cotización.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 border border-green-200
                            dark:bg-green-900/30 dark:text-green-100 dark:border-green-900">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200
                            dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 🔎 Buscador / Filtros --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 mb-6">
                <form method="GET" action="{{ route('admin.ventas.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    {{-- Buscar --}}
                    <div class="md:col-span-7">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">
                            Buscar
                        </label>

                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="M21 21l-4.3-4.3"></path>
                                </svg>
                            </span>

                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="ID de venta/cotización, nombre o correo…"
                                class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                       bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Tip: prueba con “samuel”, “@gmail.com” o un número.
                        </p>
                    </div>

                    {{-- Estado --}}
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">
                            Estado
                        </label>

                        <select
                            name="estado"
                            class="w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                                   bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Todos</option>
                            <option value="pendiente_pago" {{ request('estado') === 'pendiente_pago' ? 'selected' : '' }}>
                                Pendiente pago
                            </option>
                            <option value="pagada" {{ request('estado') === 'pagada' ? 'selected' : '' }}>
                                Pagada
                            </option>
                            <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>
                                Cancelada
                            </option>
                        </select>
                    </div>

                    {{-- Botones --}}
                    <div class="md:col-span-2 flex md:items-end gap-2">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl
                                   bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-sm"
                        >
                            Filtrar
                        </button>

                        <a
                            href="{{ route('admin.ventas.index') }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl
                                   bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold
                                   dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                        >
                            Limpiar
                        </a>
                    </div>
                </form>

                {{-- Resumen rápido --}}
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Ventas encontradas</div>
                        <div class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalVentas }}</div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Ingresos cobrados (pagadas)</div>
                        <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
                            ${{ number_format((float)$ingresosCobrados, 2, ',', '.') }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400">Por cobrar (pendientes)</div>
                        <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
                            ${{ number_format((float)$porCobrar, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="py-2">#</th>
                            <th class="py-2">Cliente</th>
                            <th class="py-2">Estado</th>
                            <th class="py-2">Pago</th>
                            <th class="py-2 text-right">Total venta</th>
                            <th class="py-2 text-right">Creada</th>
                            <th class="py-2 text-right">Acción</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($ventas as $v)
                            @php
                                $estado = $v->estado_venta ?? 'pendiente_pago';
                                $badgeEstado = match($estado) {
                                    'pagada' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                                    'cancelada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                                    default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                                };

                                $cliente = $v->usuario ?? $v->cotizacion->usuario ?? null;

                                $metodo = $v->metodo_pago ?: '—';

                                $compEstado = $v->comprobante_estado ?? null; // pendiente_revision | aceptado | rechazado
                                $tieneComp = !empty($v->comprobante_path);

                                $badgeComp = match($compEstado) {
                                    'aceptado' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                                    'rechazado' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                                    default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                                };
                            @endphp

                            <tr class="border-t dark:border-gray-700 align-top">
                                <td class="py-3 font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $v->id }}
                                </td>

                                <td class="py-3">
                                    <div class="text-gray-900 dark:text-gray-100 font-semibold">
                                        {{ $cliente->name ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $cliente->email ?? '' }}
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                        Cotización: #{{ $v->cotizacion_id }}
                                    </div>
                                </td>

                                <td class="py-3">
                                    <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 inline-flex {{ $badgeEstado }}">
                                        {{ strtoupper(str_replace('_',' ', $estado)) }}
                                    </span>
                                </td>

                                {{-- Pago --}}
                                <td class="py-3">
                                    <div class="text-gray-900 dark:text-gray-100 font-semibold">
                                        Método: <span class="font-extrabold">{{ strtoupper($metodo) }}</span>
                                    </div>

                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                        Comprobante:
                                        @if(!$tieneComp)
                                            <span class="font-bold">—</span>
                                        @else
                                            <span class="text-xs font-extrabold px-2 py-0.5 rounded-full ring-1 inline-flex {{ $badgeComp }}">
                                                {{ strtoupper(str_replace('_',' ', $compEstado ?: 'pendiente_revision')) }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($tieneComp && !empty($v->comprobante_subido_en))
                                        <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                            Subido: {{ \Carbon\Carbon::parse($v->comprobante_subido_en)->format('Y-m-d H:i') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3 text-right font-extrabold text-gray-900 dark:text-gray-100">
                                    ${{ number_format((float)$v->total_venta, 2, ',', '.') }}
                                </td>

                                <td class="py-3 text-right text-gray-700 dark:text-gray-200">
                                    {{ optional($v->created_at)->format('Y-m-d H:i') }}
                                </td>

                                <td class="py-3 text-right">
                                    <a class="text-blue-600 hover:underline font-semibold"
                                       href="{{ route('admin.ventas.show', $v->id) }}">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t dark:border-gray-700">
                                <td colspan="7" class="py-10 text-center text-gray-600 dark:text-gray-300">
                                    No hay ventas con esos filtros.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if(method_exists($ventas, 'links'))
                    <div class="mt-6">
                        {{ $ventas->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
