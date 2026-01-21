<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Productos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Lista de Productos
                    </h3>
                    <a href="{{ route('admin.productos.create') }}"
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        + Crear Producto
                    </a>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">Cliente</th>
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Marca</th>
                            <th class="px-4 py-2">Modelo</th>
                            <th class="px-4 py-2">Tipo</th>
                            <th class="px-4 py-2">Capacidad</th>
                            <th class="px-4 py-2">Precio</th>
                            <th class="px-4 py-2">Stock</th>
                            <th class="px-4 py-2 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($productos as $producto)
                        <tr>
                            <td class="px-4 py-2">
                                {{ $producto->cliente->name ?? 'Sin cliente' }}
                            </td>
                            <td class="px-4 py-2">{{ $producto->nombre }}</td>
                            <td class="px-4 py-2">{{ $producto->marca }}</td>
                            <td class="px-4 py-2">{{ $producto->modelo }}</td>
                            <td class="px-4 py-2">{{ $producto->tipo }}</td>
                            <td class="px-4 py-2">{{ $producto->capacidad }}</td>
                            <td class="px-4 py-2">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2">{{ $producto->stock }}</td>
                            <td class="px-4 py-2 text-center">
                                <a href="{{ route('admin.productos.edit', $producto->id) }}"
                                   class="bg-blue-500 text-white px-3 py-1 rounded">
                                    Editar
                                </a>

                                <form action="{{ route('admin.productos.destroy', $producto->id) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded"
                                            onclick="return confirm('¿Eliminar producto?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>
