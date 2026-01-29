<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Editar proveedor
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Actualiza contacto, ubicación y notas del proveedor.
                </p>
            </div>

            <a href="{{ route('admin.proveedores.index') }}"
               class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                      dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800
                            dark:border-red-900 dark:bg-red-900/30 dark:text-red-100">
                    <div class="font-extrabold mb-2">Revisa estos errores:</div>
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                {{-- ✅ aquí está el fix --}}
                <form method="POST" action="{{ route('admin.proveedores.update', $proveedor) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 dark:text-gray-300 mb-1">
                                Nombre *
                            </label>
                            <input name="nombre" required
                                   value="{{ old('nombre', $proveedor->nombre) }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                                          bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 dark:text-gray-300 mb-1">
                                Contacto
                            </label>
                            <input name="contacto"
                                   value="{{ old('contacto', $proveedor->contacto) }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                                          bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 dark:text-gray-300 mb-1">
                                Email
                            </label>
                            <input type="email" name="email"
                                   value="{{ old('email', $proveedor->email) }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                                          bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 dark:text-gray-300 mb-1">
                                Whatsapp
                            </label>
                            <input name="whatsapp"
                                   value="{{ old('whatsapp', $proveedor->whatsapp) }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                                          bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 dark:text-gray-300 mb-1">
                                País
                            </label>
                            <input name="pais"
                                   value="{{ old('pais', $proveedor->pais) }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                                          bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 dark:text-gray-300 mb-1">
                                Ciudad
                            </label>
                            <input name="ciudad"
                                   value="{{ old('ciudad', $proveedor->ciudad) }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                                          bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-gray-600 dark:text-gray-300 mb-1">
                            Notas
                        </label>
                        <textarea name="notas" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 dark:border-gray-700
                                         bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-4">{{ old('notas', $proveedor->notas) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.proveedores.index') }}"
                           class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                                  dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100">
                            Cancelar
                        </a>

                        <button class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
