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

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="py-2">#</th>
                            <th class="py-2">Cliente</th>
                            <th class="py-2">Estado</th>
                            <th class="py-2 text-right">Total venta</th>
                            <th class="py-2 text-right">Creada</th>
                            <th class="py-2 text-right">Acción</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($ventas as $v)

                            @php
                                $estado = $v->estado_venta ?? 'pendiente_pago';
                                $badge = match($estado) {
                                    'pagada' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                                    'cancelada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                                    default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                                };
                            @endphp

                            <tr class="border-t dark:border-gray-700">
                                <td class="py-3 font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $v->id }}
                                </td>

                                <td class="py-3">
                                    <div class="text-gray-900 dark:text-gray-100 font-semibold">
                                        {{ $v->usuario->name ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $v->usuario->email ?? '' }}
                                    </div>
                                </td>

                                <td class="py-3">
                                    <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 {{ $badge }}">
                                        {{ strtoupper(str_replace('_',' ', $estado)) }}
                                    </span>
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
                                <td colspan="6" class="py-10 text-center text-gray-600 dark:text-gray-300">
                                    Aún no hay ventas. Se crearán cuando un cliente acepte una cotización.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
