<x-app-layout>
    <x-slot name="header">
        @include('cliente.cotizaciones.partials.header', [
            'cotizaciones' => $cotizaciones,
            'pendientes' => $pendientes
        ])
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @include('cliente.cotizaciones.partials.alerts')

            @include('cliente.cotizaciones.partials.resumen', [
                'cotizaciones' => $cotizaciones,
                'pendientes' => $pendientes
            ])

            <div class="space-y-6">
                @forelse($cotizaciones as $c)
                    @include('cliente.cotizaciones.partials.card', ['c' => $c])
                @empty
                    <div class="rounded-2xl border border-gray-100 bg-white p-8 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        No tienes cotizaciones aún.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    @include('cliente.cotizaciones.partials.modal-respuesta')
    @include('cliente.cotizaciones.partials.modal-imagen')
    @include('cliente.cotizaciones.partials.scripts')
</x-app-layout>
