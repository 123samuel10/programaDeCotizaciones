<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                    Editar cliente
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Actualiza datos del cliente (persona o empresa).
                </p>
            </div>

            <a href="{{ route('admin.clientes.index') }}"
               class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    <ul class="list-disc ml-6 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-100 dark:border-green-900">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">

                <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Nombre</label>
                        <input name="name" value="{{ old('name', $cliente->name) }}"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Empresa (opcional)</label>
                        <input name="empresa" value="{{ old('empresa', $cliente->empresa) }}"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @error('empresa') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Email</label>
                        <input name="email" type="email" value="{{ old('email', $cliente->email) }}"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">
                            Nueva contraseña (opcional)
                        </label>
                        <input name="password" type="password" placeholder="Dejar vacío para no cambiarla"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Si no escribes nada, la contraseña se mantiene igual.
                        </p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.clientes.index') }}"
                           class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                            Cancelar
                        </a>

                        <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                            Guardar cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
