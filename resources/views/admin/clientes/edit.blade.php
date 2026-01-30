{{-- resources/views/admin/clientes/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                    Editar cliente
                </h2>

                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $cliente->name }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $cliente->empresa ?? 'Sin empresa' }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $cliente->email }}</span>
                </div>
            </div>

            <a href="{{ route('admin.clientes.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                <span>←</span> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Alerts --}}
            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    <div class="font-semibold mb-2">Revisa estos campos:</div>
                    <ul class="list-disc ml-6 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-green-50 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-100 dark:border-green-900">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- CARD PRINCIPAL --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 shadow-sm">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                Datos del cliente
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Edita la información. La contraseña es opcional.
                            </p>
                        </div>

                        <span class="text-xs px-3 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200 border border-blue-100 dark:border-blue-900">
                            ID #{{ $cliente->id }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- GRID: nombre / empresa --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input name="name" value="{{ old('name', $cliente->name) }}"
                                       placeholder="Ej: Tatiana Gómez"
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">
                                    Empresa <span class="text-xs text-gray-400">(opcional)</span>
                                </label>
                                <input name="empresa" value="{{ old('empresa', $cliente->empresa) }}"
                                       placeholder="Ej: GeniusSoft S.A.S"
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @error('empresa') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input name="email" type="email" value="{{ old('email', $cliente->email) }}"
                                   placeholder="Ej: cliente@correo.com"
                                   class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Datos para cotización --}}
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/40 flex items-start justify-between gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Datos para cotización (opcional)
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        País, ciudad, dirección, teléfono y NIT para cotizaciones más limpias.
                                    </p>
                                </div>

                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200 border border-blue-100 dark:border-blue-900">
                                    Pro
                                </span>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">País</label>
                                        <input name="pais" value="{{ old('pais', $cliente->pais) }}"
                                               placeholder="Ej: Colombia"
                                               class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @error('pais') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Ciudad</label>
                                        <input name="ciudad" value="{{ old('ciudad', $cliente->ciudad) }}"
                                               placeholder="Ej: Bogotá"
                                               class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @error('ciudad') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Dirección</label>
                                        <input name="direccion" value="{{ old('direccion', $cliente->direccion) }}"
                                               placeholder="Ej: Cra 12 # 45-67"
                                               class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @error('direccion') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Teléfono</label>
                                        <input name="telefono" value="{{ old('telefono', $cliente->telefono) }}"
                                               placeholder="Ej: +57 300 000 0000"
                                               class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @error('telefono') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">NIT</label>
                                        <input name="nit" value="{{ old('nit', $cliente->nit) }}"
                                               placeholder="Ej: 900123456-7"
                                               class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @error('nit') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Password con toggle --}}
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/40 flex items-start justify-between gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Cambiar contraseña (opcional)
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Déjalo vacío si no quieres cambiarla.
                                    </p>
                                </div>

                                <button type="button" id="togglePassBtn"
                                        class="px-3 py-1.5 rounded-xl bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold">
                                    Mostrar
                                </button>
                            </div>

                            <div class="p-4">
                                <input name="password" id="passwordInput" type="password"
                                       placeholder="Nueva contraseña (mínimo 6 caracteres)"
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-end pt-2">
                            <a href="{{ route('admin.clientes.index') }}"
                               class="inline-flex justify-center px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                                Cancelar
                            </a>

                            <button class="inline-flex justify-center px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

                {{-- SIDEBAR: RESUMEN --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 shadow-sm h-fit">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        Resumen
                    </h3>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">Nombre</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right break-words">
                                {{ $cliente->name }}
                            </span>
                        </div>

                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">Empresa</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right break-words">
                                {{ $cliente->empresa ?? '—' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">Email</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right break-all">
                                {{ $cliente->email }}
                            </span>
                        </div>

                        {{-- NUEVOS CAMPOS --}}
                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">País</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right break-words">
                                {{ $cliente->pais ?? '—' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">Ciudad</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right break-words">
                                {{ $cliente->ciudad ?? '—' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">Dirección</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right break-words">
                                {{ $cliente->direccion ?? '—' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">Teléfono</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right break-words">
                                {{ $cliente->telefono ?? '—' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">NIT</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right break-words">
                                {{ $cliente->nit ?? '—' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-12 gap-3 items-start">
                            <span class="col-span-4 text-gray-500 dark:text-gray-400">Creado</span>
                            <span class="col-span-8 font-semibold text-gray-900 dark:text-gray-100 text-right">
                                {{ optional($cliente->created_at)->format('Y-m-d') ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 rounded-2xl bg-blue-50 text-blue-800 border border-blue-100 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-900">
                        <div class="font-semibold mb-1">Tip</div>
                        <p class="text-sm">
                            Si el cliente es empresa, llena <b>Empresa</b> y <b>NIT</b> para cotizaciones más claras.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('togglePassBtn');
            const input = document.getElementById('passwordInput');
            if (!btn || !input) return;

            btn.addEventListener('click', () => {
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                btn.textContent = isPass ? 'Ocultar' : 'Mostrar';
            });
        })();
    </script>
</x-app-layout>
