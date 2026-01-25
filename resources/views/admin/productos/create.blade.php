<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            Nuevo Producto (Standard EXWORKS)
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    <ul class="list-disc ml-6 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                <form method="POST"
                      action="{{ route('admin.productos.store') }}"
                      enctype="multipart/form-data"
                      class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <input name="marca" placeholder="Marca"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('marca') }}">

                    <input name="modelo" placeholder="Modelo"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('modelo') }}">

                    <div class="md:col-span-2">
                        <input name="nombre_producto" placeholder="Nombre del producto"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            value="{{ old('nombre_producto') }}">
                    </div>

                    <div class="md:col-span-2">
                        <textarea name="descripcion" rows="4" placeholder="Descripción"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('descripcion') }}</textarea>
                    </div>

                    {{-- FOTO --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Foto del producto</label>

                        <div class="flex items-center gap-4">
                            <div class="w-24 h-24 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 overflow-hidden flex items-center justify-center">
                                <span class="text-xs text-gray-400">Sin foto</span>
                            </div>

                            <input type="file" name="foto" accept="image/*"
                                   class="block w-full text-sm text-gray-600 dark:text-gray-300
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-xl file:border-0
                                          file:bg-gray-100 file:text-gray-800
                                          hover:file:bg-gray-200
                                          dark:file:bg-gray-700 dark:file:text-gray-100 dark:hover:file:bg-gray-600">
                        </div>

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            JPG / PNG / WEBP · máximo 4MB.
                        </p>
                    </div>

                    <input name="repisas_iluminadas" type="number" min="0" step="1"
                        placeholder="# Repisas Iluminadas"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('repisas_iluminadas') }}">

                    <input name="refrigerante" placeholder="Refrigerante (ej: HFC)"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('refrigerante') }}">

                    <input name="longitud" type="number" placeholder="Longitud (mm)"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('longitud') }}">

                    <input name="profundidad" type="number" placeholder="Profundidad (mm)"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('profundidad') }}">

                    <input name="altura" type="number" placeholder="Altura (mm)"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('altura') }}">

                    <input name="precio_base_venta" type="number" step="0.01" placeholder="Base venta (EXWORKS)"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('precio_base_venta') }}">

                    <input name="precio_base_costo" type="number" step="0.01" placeholder="Base costo"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('precio_base_costo') }}">

                    <div class="md:col-span-2 flex justify-end gap-3">
                        <a href="{{ route('admin.productos.index') }}"
                           class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                            Cancelar
                        </a>
                        <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
