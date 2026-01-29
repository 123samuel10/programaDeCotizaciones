{{-- resources/views/layouts/navigation.blade.php --}}

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT -->
            <div class="flex items-center">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    {{-- Si no hay sesión, que no intente ir a dashboard --}}
                    <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Links Desktop -->
                <div class="hidden sm:flex sm:items-center sm:ms-10 gap-2">
                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            Panel
                        </x-nav-link>

                        @if(auth()->user()->role === 'admin')
                            <x-nav-link :href="route('admin.productos.index')" :active="request()->routeIs('admin.productos.*')">
                                Productos
                            </x-nav-link>

                            <x-nav-link :href="route('admin.cotizaciones.index')" :active="request()->routeIs('admin.cotizaciones.*')">
                                Cotizaciones
                            </x-nav-link>

                            <x-nav-link :href="route('admin.ventas.index')" :active="request()->routeIs('admin.ventas.*')">
                                Ventas
                            </x-nav-link>

                            <x-nav-link :href="route('admin.clientes.index')" :active="request()->routeIs('admin.clientes.*')">
                                Clientes
                            </x-nav-link>

                            {{--  Seguimientos (solo si existe la ruta) --}}
                            @if(Route::has('admin.seguimientos.index'))
                                <x-nav-link :href="route('admin.seguimientos.index')" :active="request()->routeIs('admin.seguimientos.*')">
                                    Seguimientos
                                </x-nav-link>
                            @endif

                            {{--  Proveedores (solo si existe la ruta) --}}
                            @if(Route::has('admin.proveedores.index'))
                                <x-nav-link :href="route('admin.proveedores.index')" :active="request()->routeIs('admin.proveedores.*')">
                                    Proveedores
                                </x-nav-link>
                            @endif
                        @else
                            <x-nav-link :href="route('cliente.cotizaciones.index')" :active="request()->routeIs('cliente.cotizaciones.*')">
                                Mis cotizaciones
                            </x-nav-link>
                        @endif
                    @endauth

                    @guest
                        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                            Iniciar sesión
                        </x-nav-link>
                    @endguest
                </div>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @auth
                    @php $rol = auth()->user()->role ?? 'cliente'; @endphp

                    <span class="text-[11px] font-bold px-3 py-1 rounded-full
                        {{ $rol === 'admin'
                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'
                            : 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-200' }}">
                        {{ strtoupper($rol) }}
                    </span>

                    <!-- Dropdown Usuario -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-3 px-3 py-2 rounded-lg
                                           hover:bg-gray-100 dark:hover:bg-gray-700/50 transition">

                                <div class="h-9 w-9 rounded-full bg-gray-100 dark:bg-gray-700
                                            ring-1 ring-gray-200 dark:ring-gray-600
                                            flex items-center justify-center font-extrabold text-gray-700 dark:text-gray-200">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>

                                <div class="hidden lg:block text-left">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ auth()->user()->email }}
                                    </div>
                                </div>

                                <svg class="fill-current h-4 w-4 text-gray-500 dark:text-gray-300"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>

                            <x-dropdown-link :href="route('profile.edit')">
                                Perfil
                            </x-dropdown-link>

                            <div class="border-t border-gray-100 dark:border-gray-700"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                    Cerrar sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                              dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        Iniciar sesión
                    </a>
                @endguest
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400
                               hover:bg-gray-100 dark:hover:bg-gray-900 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Panel
                </x-responsive-nav-link>

                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.productos.index')" :active="request()->routeIs('admin.productos.*')">
                        Productos
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.cotizaciones.index')" :active="request()->routeIs('admin.cotizaciones.*')">
                        Cotizaciones
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.ventas.index')" :active="request()->routeIs('admin.ventas.*')">
                        Ventas
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.clientes.index')" :active="request()->routeIs('admin.clientes.*')">
                        Clientes
                    </x-responsive-nav-link>

                    {{-- Seguimientos mobile --}}
                    @if(Route::has('admin.seguimientos.index'))
                        <x-responsive-nav-link :href="route('admin.seguimientos.index')" :active="request()->routeIs('admin.seguimientos.*')">
                            Seguimientos
                        </x-responsive-nav-link>
                    @endif

                    {{-- Proveedores mobile --}}
                    @if(Route::has('admin.proveedores.index'))
                        <x-responsive-nav-link :href="route('admin.proveedores.index')" :active="request()->routeIs('admin.proveedores.*')">
                            Proveedores
                        </x-responsive-nav-link>
                    @endif
                @else
                    <x-responsive-nav-link :href="route('cliente.cotizaciones.index')" :active="request()->routeIs('cliente.cotizaciones.*')">
                        Mis cotizaciones
                    </x-responsive-nav-link>
                @endif
            @endauth

            @guest
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                    Iniciar sesión
                </x-responsive-nav-link>
            @endguest
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ auth()->user()->email }}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Perfil
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Cerrar sesión
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
