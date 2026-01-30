{{-- resources/views/admin/clientes/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                    Nuevo cliente
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Registra un cliente (persona natural o empresa) para cotizaciones.
                </p>
            </div>

            <a href="{{ route('admin.clientes.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- ERRORES --}}
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{--FORM PRINCIPAL --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">
                        Datos del cliente
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Completa la información básica del cliente.
                    </p>

                    <form method="POST" action="{{ route('admin.clientes.store') }}" class="space-y-6">
                        @csrf

                        {{-- Nombre / Empresa --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input name="name" value="{{ old('name') }}"
                                       placeholder="Ej: Tatiana Gómez"
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">
                                    Empresa <span class="text-xs text-gray-400">(opcional)</span>
                                </label>
                                <input name="empresa" value="{{ old('empresa') }}"
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
                            <input name="email" type="email" value="{{ old('email') }}"
                                   placeholder="cliente@correo.com"
                                   class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- DATOS PARA COTIZACIÓN --}}
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/40 flex items-start justify-between gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Datos para cotización <span class="text-xs text-gray-400">(opcional)</span>
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        País, ciudad, dirección, teléfono y NIT ayudan a que la cotización se vea profesional.
                                    </p>
                                </div>
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700
                                             dark:bg-blue-900/30 dark:text-blue-200 border border-blue-100 dark:border-blue-900">
                                    Pro
                                </span>
                            </div>

                            <div class="p-4 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">País</label>
                                        <select name="pais"
                                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                            <option value="">Selecciona un país</option>
                                            @php $pais = old('pais'); @endphp
                                            <option value="Colombia" {{ $pais==='Colombia' ? 'selected' : '' }}>Colombia</option>
                                            <option value="México" {{ $pais==='México' ? 'selected' : '' }}>México</option>
                                            <option value="Perú" {{ $pais==='Perú' ? 'selected' : '' }}>Perú</option>
                                            <option value="Ecuador" {{ $pais==='Ecuador' ? 'selected' : '' }}>Ecuador</option>
                                            <option value="Chile" {{ $pais==='Chile' ? 'selected' : '' }}>Chile</option>
                                            <option value="Argentina" {{ $pais==='Argentina' ? 'selected' : '' }}>Argentina</option>
                                            <option value="Estados Unidos" {{ $pais==='Estados Unidos' ? 'selected' : '' }}>Estados Unidos</option>
                                            <option value="España" {{ $pais==='España' ? 'selected' : '' }}>España</option>
                                        </select>
                                        @error('pais') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Ciudad</label>
                                        <input name="ciudad" value="{{ old('ciudad') }}"
                                               placeholder="Ej: Bogotá"
                                               class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @error('ciudad') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Dirección</label>
                                    <input name="direccion" value="{{ old('direccion') }}"
                                           placeholder="Ej: Cra 12 # 45-67"
                                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    @error('direccion') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Teléfono</label>
                                        <input name="telefono" value="{{ old('telefono') }}"
                                               placeholder="Ej: +57 300 000 0000"
                                               class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @error('telefono') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">NIT</label>
                                        <input name="nit" value="{{ old('nit') }}"
                                               placeholder="Ej: 900123456-7"
                                               class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @error('nit') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/40 flex items-start justify-between gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Contraseña
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Mínimo 6 caracteres.
                                    </p>
                                </div>

                                <button type="button" id="togglePassBtn"
                                        class="px-3 py-1.5 rounded-xl bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold">
                                    Mostrar
                                </button>
                            </div>

                            <div class="p-4">
                                <input name="password" id="passwordInput" type="password"
                                       placeholder="Contraseña segura"
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
                                Crear cliente
                            </button>
                        </div>
                    </form>
                </div>

                {{-- SIDEBAR --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 shadow-sm h-fit">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        ¿Para qué sirve?
                    </h3>

                    <ul class="mt-4 text-sm space-y-3 text-gray-700 dark:text-gray-200">
                        <li>• Asociar cotizaciones a personas o empresas.</li>
                        <li>• Mantener histórico por cliente.</li>
                        <li>• Facilitar seguimiento comercial.</li>
                    </ul>

                    <div class="mt-6 p-4 rounded-2xl bg-blue-50 text-blue-800 border border-blue-100 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-900">
                        <div class="font-semibold mb-1">Tip</div>
                        <p class="text-sm">
                            Si el cliente es una empresa, llena <b>Empresa</b> y <b>NIT</b> para cotizaciones más claras.
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
