<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Información del Perfil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Actualiza la información de tu cuenta y tus datos para cotización.
        </p>
    </header>

    <!-- Formulario para reenviar verificación de correo -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Formulario para actualizar perfil -->
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Nombre -->
        <div>
            <x-input-label for="name" :value="'Nombre'" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Correo Electrónico -->
        <div>
            <x-input-label for="email" :value="'Correo Electrónico'" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Tu correo electrónico no ha sido verificado.

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Haz clic aquí para reenviar el correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Datos para cotización -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/40">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                    Datos para cotización (opcional)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Estos datos aparecen en tus cotizaciones y ayudan a que se vean más profesionales.
                </p>
            </div>

            <div class="p-4 space-y-4">
                <!-- Empresa -->
                <div>
                    <x-input-label for="empresa" :value="'Empresa (opcional)'" />
                    <x-text-input id="empresa" name="empresa" type="text" class="mt-1 block w-full"
                        :value="old('empresa', $user->empresa)" autocomplete="organization" />
                    <x-input-error class="mt-2" :messages="$errors->get('empresa')" />

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Si eres empresa, escríbela para que aparezca en tus cotizaciones.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- País -->
                    <div>
                        <x-input-label for="pais" :value="'País'" />
                        <x-text-input id="pais" name="pais" type="text" class="mt-1 block w-full"
                            :value="old('pais', $user->pais)" autocomplete="country-name" />
                        <x-input-error class="mt-2" :messages="$errors->get('pais')" />
                    </div>

                    <!-- Ciudad -->
                    <div>
                        <x-input-label for="ciudad" :value="'Ciudad'" />
                        <x-text-input id="ciudad" name="ciudad" type="text" class="mt-1 block w-full"
                            :value="old('ciudad', $user->ciudad)" autocomplete="address-level2" />
                        <x-input-error class="mt-2" :messages="$errors->get('ciudad')" />
                    </div>
                </div>

                <!-- Dirección -->
                <div>
                    <x-input-label for="direccion" :value="'Dirección'" />
                    <x-text-input id="direccion" name="direccion" type="text" class="mt-1 block w-full"
                        :value="old('direccion', $user->direccion)" autocomplete="street-address" />
                    <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Teléfono -->
                    <div>
                        <x-input-label for="telefono" :value="'Teléfono'" />
                        <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full"
                            :value="old('telefono', $user->telefono)" autocomplete="tel" />
                        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                    </div>

                    <!-- NIT -->
                    <div>
                        <x-input-label for="nit" :value="'NIT'" />
                        <x-text-input id="nit" name="nit" type="text" class="mt-1 block w-full"
                            :value="old('nit', $user->nit)" autocomplete="off" />
                        <x-input-error class="mt-2" :messages="$errors->get('nit')" />

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Recomendado si eres empresa (para facturación/cotización).
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón guardar -->
        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">
                    Guardado.
                </p>
            @endif
        </div>
    </form>
</section>
