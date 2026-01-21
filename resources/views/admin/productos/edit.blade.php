<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Producto
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.productos.update', $producto->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Nombre:</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Marca:</label>
                        <select name="marca" class="w-full border rounded p-2" required>
                            @php
                                $marcas = ['LG', 'Samsung', 'Whirlpool', 'Mabe'];
                            @endphp
                            @foreach($marcas as $marca)
                                <option value="{{ $marca }}" {{ $producto->marca == $marca ? 'selected' : '' }}>{{ $marca }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Tipo:</label>
                        <select name="tipo" class="w-full border rounded p-2" required>
                            @php
                                $tipos = ['Nevera', 'Congelador', 'Microondas', 'Horno'];
                            @endphp
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo }}" {{ $producto->tipo == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Capacidad:</label>
                        <input type="text" name="capacidad" value="{{ old('capacidad', $producto->capacidad) }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Modelo:</label>
                        <input type="text" name="modelo" value="{{ old('modelo', $producto->modelo) }}" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Peso:</label>
                        <input type="text" name="peso" value="{{ old('peso', $producto->peso) }}" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Dimensiones:</label>
                        <input type="text" name="dimensiones" value="{{ old('dimensiones', $producto->dimensiones) }}" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Color:</label>
                        <input type="text" name="color" value="{{ old('color', $producto->color) }}" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Precio:</label>
                        <input type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio) }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Stock:</label>
                        <input type="number" name="stock" value="{{ old('stock', $producto->stock) }}" class="w-full border rounded p-2" required>
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Actualizar Producto
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
