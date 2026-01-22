<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Control
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Bienvenida --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    ¡Bienvenido de nuevo, {{ Auth::user()->name }}!
                </h3>
                <p class="text-gray-700 dark:text-gray-300">
                    @if(Auth::user()->role === 'admin')
                        Desde aquí puedes gestionar tus productos, revisar estadísticas y administrar tu tienda de manera segura.
                    @else
                        Bienvenido a tu panel, aquí puedes ver tus compras y tu información personal.
                    @endif
                </p>
            </div>

            {{-- Panel de acciones rápidas solo para admin --}}
            @if(Auth::user()->role === 'admin')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Card: Ver Productos --}}
                <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6 flex flex-col justify-between hover:shadow-lg transition duration-200">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Productos</h4>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            Visualiza todos tus productos, edítalos o elimina los que ya no necesites.
                        </p>
                    </div>
                    <a href="{{ route('admin.productos.index') }}"
                       class="mt-auto inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center">
                        Ver Productos
                    </a>
                </div>

                {{-- Card: Agregar Producto --}}
                {{-- <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6 flex flex-col justify-between hover:shadow-lg transition duration-200">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Agregar Producto</h4>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            Crea nuevos productos seleccionando sus atributos y detalles.
                        </p>
                    </div>
                    <a href="{{ route('admin.productos.create') }}"
                       class="mt-auto inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center">
                        Agregar Producto
                    </a>
                </div> --}}

            </div>
            @endif

        </div>
    </div>
</x-app-layout>
