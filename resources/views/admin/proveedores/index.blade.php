<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Proveedores
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                    Gestiona proveedores para seguimiento de importación: contacto, país, ciudad y notas.
                </p>
            </div>

            <a href="{{ route('admin.proveedores.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                      bg-blue-600 hover:bg-blue-700 text-white font-extrabold shadow-sm">
                + Nuevo proveedor
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alerts reutilizando tu estilo --}}
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800
                            dark:border-green-900 dark:bg-green-900/30 dark:text-green-100">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="text-sm font-semibold">{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800
                            dark:border-red-900 dark:bg-red-900/30 dark:text-red-100">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 9v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <div class="text-sm font-semibold">{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            {{-- Buscador --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <form method="GET" action="{{ route('admin.proveedores.index') }}" class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <div class="flex-1">
                        <label class="block text-xs font-extrabold text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                {{-- icono lupa --}}
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </span>
                            <input type="text" name="q" value="{{ $q ?? '' }}"
                                   placeholder="Nombre, contacto, email, whatsapp, país, ciudad..."
                                   class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                          bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="flex gap-2 sm:pt-6">
                        <button class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800
                                       dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                            Buscar
                        </button>

                        <a href="{{ route('admin.proveedores.index') }}"
                           class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                                  dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="py-3 px-4 text-left font-extrabold text-gray-700 dark:text-gray-200">Proveedor</th>
                                <th class="py-3 px-4 text-left font-extrabold text-gray-700 dark:text-gray-200">Contacto</th>
                                <th class="py-3 px-4 text-left font-extrabold text-gray-700 dark:text-gray-200">Ubicación</th>
                                <th class="py-3 px-4 text-right font-extrabold text-gray-700 dark:text-gray-200">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proveedores as $p)
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="py-3 px-4">
                                        <div class="font-extrabold text-gray-900 dark:text-gray-100">{{ $p->nombre }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $p->email ?? '—' }}
                                            @if($p->whatsapp) · {{ $p->whatsapp }} @endif
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <div class="text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ $p->contacto ?? '—' }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <div class="text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ $p->pais ?? '—' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $p->ciudad ?? '—' }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.proveedores.edit', $p->id) }}"
                                               class="px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs">
                                                Editar
                                            </a>

                                            <form method="POST" action="{{ route('admin.proveedores.destroy', $p->id) }}"
                                                  onsubmit="return confirm('¿Eliminar proveedor? Si tiene seguimientos asociados no se eliminará.')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-3 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 px-4 text-center text-gray-600 dark:text-gray-300">
                                        No hay proveedores aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($proveedores, 'links'))
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $proveedores->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
