<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Crear Producto
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
               <form action="{{ route('admin.productos.store') }}" method="POST">

                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Nombre:</label>
                        <input type="text" name="nombre" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Marca:</label>
                        <select name="marca" class="w-full border rounded p-2" required>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca }}">{{ $marca }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Tipo:</label>
                        <select name="tipo" class="w-full border rounded p-2" required>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo }}">{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Color:</label>
                        <select name="color" class="w-full border rounded p-2" required>
                            @foreach($colores as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Capacidad:</label>
                        <select name="capacidad" class="w-full border rounded p-2" required>
                            @foreach($capacidades as $capacidad)
                                <option value="{{ $capacidad }}">{{ $capacidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Modelo:</label>
                        <input type="text" name="modelo" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Peso:</label>
                        <input type="text" name="peso" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Dimensiones:</label>
                        <input type="text" name="dimensiones" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Precio:</label>
                        <input type="number" name="precio" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Stock:</label>
                        <input type="number" name="stock" class="w-full border rounded p-2" required>
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Guardar Producto
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
