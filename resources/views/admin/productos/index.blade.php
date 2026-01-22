<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Productos
            </h2>

            <a href="{{ route('admin.productos.create') }}"
               class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                + Nuevo producto
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-100 dark:border-green-900">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($productos as $producto)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ $producto->nombre_producto }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $producto->marca }} · {{ $producto->modelo }}
                        </p>

                        <div class="mt-4 text-sm text-gray-700 dark:text-gray-200 space-y-1">
                            <div class="flex justify-between">
                                <span>Base Venta (EXWORKS)</span>
                                <span class="font-semibold">${{ number_format($producto->precio_base_venta, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Base Costo</span>
                                <span class="font-semibold">${{ number_format($producto->precio_base_costo, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.productos.edit', $producto) }}"
                           class="mt-5 inline-flex w-full justify-center px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                            Abrir / Editar cotización
                        </a>
                    </div>
                @empty
                    <div class="col-span-full p-8 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-700 dark:text-gray-200">
                        No hay productos aún. Crea el primero con el botón <b>Nuevo producto</b>.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
