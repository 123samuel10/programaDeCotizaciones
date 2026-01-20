<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Productos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Lista de Productos</h3>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Marca</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Capacidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Peso</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Dimensiones</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Color</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Stock</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach($productos as $producto)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->marca }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->modelo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->tipo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->capacidad }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->peso }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->dimensiones }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->color }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($producto->precio, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $producto->stock }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
