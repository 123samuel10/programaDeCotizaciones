<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                Cotizaciones
            </h2>

            <a href="{{ route('admin.cotizaciones.create') }}"
               class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                + Nueva cotización
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="py-2">#</th>
                                <th class="py-2">Cliente</th>
                                <th class="py-2 text-center">Líneas</th>
                                <th class="py-2 text-right">Total Venta</th>
                                <th class="py-2 text-right">Total Costo</th>
                                <th class="py-2 text-right">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($cotizaciones as $c)
                                <tr class="border-t dark:border-gray-700">
                                    <td class="py-3 font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $c->id }}
                                    </td>

                                    <td class="py-3">
                                        <div class="text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ $c->usuario->name ?? '—' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $c->usuario->email ?? '' }}
                                        </div>
                                    </td>

                                    <td class="py-3 text-center">
                                        {{ $c->items()->count() }}
                                    </td>

                                    <td class="py-3 text-right text-gray-900 dark:text-gray-100">
                                        ${{ number_format((float) $c->total_venta, 2, ',', '.') }}
                                    </td>

                                    <td class="py-3 text-right text-gray-900 dark:text-gray-100">
                                        ${{ number_format((float) $c->total_costo, 2, ',', '.') }}
                                    </td>

                                    <td class="py-3 text-right">
                                        <a class="text-blue-600 hover:underline font-semibold"
                                           href="{{ route('admin.cotizaciones.edit', $c->id) }}">
                                            Abrir
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t dark:border-gray-700">
                                    <td colspan="6" class="py-8 text-center text-gray-600 dark:text-gray-300">
                                        No hay cotizaciones aún.
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
