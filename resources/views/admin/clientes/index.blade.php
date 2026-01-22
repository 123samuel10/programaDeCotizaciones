<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                Clientes
            </h2>

            <a href="{{ route('admin.clientes.create') }}"
               class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                + Nuevo cliente
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Empresa</th>
                            <th class="py-2">Email</th>
                            <th class="py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $c)
                            <tr class="border-t dark:border-gray-700">
                                <td class="py-3 font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $c->name }}
                                </td>
                                <td class="py-3 text-gray-700 dark:text-gray-200">
                                    {{ $c->empresa ?? '—' }}
                                </td>
                                <td class="py-3 text-gray-700 dark:text-gray-200">
                                    {{ $c->email }}
                                </td>
                                <td class="py-3 text-right space-x-2">
                                    <a href="{{ route('admin.clientes.edit', $c) }}"
                                       class="text-blue-600 hover:underline font-semibold">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('admin.clientes.destroy', $c) }}"
                                          class="inline"
                                          onsubmit="return confirm('¿Eliminar cliente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline font-semibold">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                    No hay clientes aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
