<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white leading-tight">
                    Panel de Control
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Heral Enterprises · Programa de Cotizaciones
                </p>
            </div>

            <span class="text-xs font-bold px-3 py-1 rounded-full
                {{ auth()->user()->role === 'admin'
                    ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/20'
                    : 'bg-gray-100 text-gray-700 ring-1 ring-gray-200 dark:bg-gray-700/60 dark:text-gray-200 dark:ring-gray-600' }}">
                {{ strtoupper(auth()->user()->role) }}
            </span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Bienvenida --}}
            <div class="mb-8 rounded-2xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200/70 dark:ring-gray-700/60 p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                    ¡Bienvenido, {{ Auth::user()->name }}!
                </h3>

                <p class="mt-2 text-gray-600 dark:text-gray-300">
                    @if(Auth::user()->role === 'admin')
                        Resumen general del sistema y accesos rápidos para tu gestión.
                    @else
                        Revisa tus cotizaciones y su estado desde aquí.
                    @endif
                </p>

                @if(Auth::user()->role === 'admin')
                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('admin.cotizaciones.index') }}"
                           class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 font-bold text-white
                                  bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition">
                            Ir a cotizaciones
                        </a>

                        <a href="{{ route('admin.productos.index') }}"
                           class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 font-bold
                                  text-gray-700 bg-gray-100 hover:bg-gray-200 transition
                                  dark:text-gray-100 dark:bg-gray-700/60 dark:hover:bg-gray-700">
                            Ir a productos
                        </a>
                    </div>
                @endif
            </div>

            @if(Auth::user()->role === 'admin')
                {{-- Métricas --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200/70 dark:ring-gray-700/60 p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Productos registrados</p>
                        <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">
                            {{ $totalProductos ?? '—' }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200/70 dark:ring-gray-700/60 p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Clientes</p>
                        <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">
                            {{ $totalClientes ?? '—' }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200/70 dark:ring-gray-700/60 p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Cotizaciones</p>
                        <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">
                            {{ $totalCotizaciones ?? '—' }}
                        </p>
                    </div>
                </div>

            @else
                {{-- Cliente --}}
                <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200/70 dark:ring-gray-700/60 p-6">
                    <h4 class="text-lg font-extrabold text-gray-900 dark:text-white">
                        Mis cotizaciones
                    </h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Aquí verás tus solicitudes, sus totales y el estado.
                    </p>

                    <a href="{{ route('cliente.cotizaciones.index') }}"
                       class="mt-6 inline-flex items-center justify-center rounded-xl px-4 py-2.5 font-bold text-white
                              bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition">
                        Ver mis cotizaciones
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
