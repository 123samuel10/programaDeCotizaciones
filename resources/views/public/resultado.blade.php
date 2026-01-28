<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-xl text-gray-900 dark:text-gray-100">
                Resultado de la acción
            </h2>

            <a href="{{ url('/') }}"
               class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 font-bold">
                Inicio
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
                <div class="p-8 text-center">
                    <div class="mx-auto w-14 h-14 rounded-2xl flex items-center justify-center
                                bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200 mb-4">
                        ✅
                    </div>

                    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                        {{ $titulo ?? 'Listo' }}
                    </h1>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        {{ $mensaje ?? 'La acción se procesó correctamente.' }}
                    </p>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ url('/') }}"
                           class="px-6 py-2 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                                  dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                            Volver al inicio
                        </a>

                        @if(!empty($urlVer))
                            <a href="{{ $urlVer }}"
                               class="px-6 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                                Ver cotización
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
