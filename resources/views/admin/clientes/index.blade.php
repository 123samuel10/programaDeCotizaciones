
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                Clientes
            </h2>

            <a href="{{ route('admin.clientes.create') }}"
               class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm">
                + Nuevo cliente
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-100 dark:border-green-900">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700 shadow-sm">
                <div class="overflow-x-auto">
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

                                    <td class="py-3 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.clientes.edit', $c) }}"
                                               class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                                                Editar
                                            </a>

                                            {{-- BOTÓN QUE ABRE MODAL --}}
                                            <button type="button"
                                                class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold shadow-sm"
                                                data-open-delete
                                                data-delete-name="{{ e($c->name) }}"
                                                data-delete-empresa="{{ e($c->empresa ?? '') }}"
                                                data-delete-email="{{ e($c->email) }}"
                                                data-delete-action="{{ route('admin.clientes.destroy', $c) }}">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t dark:border-gray-700">
                                    <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                        No hay clientes aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MODAL PROFESIONAL (UNA SOLA VEZ) --}}
            <div id="deleteModal"
                 class="fixed inset-0 z-50 hidden"
                 aria-hidden="true">

                {{-- Backdrop --}}
                <div id="deleteBackdrop"
                     class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

                {{-- Dialog --}}
                <div class="relative min-h-screen flex items-center justify-center p-4">
                    <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">

                        {{-- Header --}}
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center">
                                        <span class="text-red-600 dark:text-red-300 text-xl">⚠️</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            Confirmar eliminación
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Esta acción es permanente y no se puede deshacer.
                                        </p>
                                    </div>
                                </div>

                                <button type="button" id="closeDeleteModalX"
                                        class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                                    ✕
                                </button>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-6">
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700">
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                    Vas a eliminar este cliente:
                                </p>

                                <div class="space-y-1">
                                    <div class="text-gray-900 dark:text-gray-100 font-semibold" id="modalClienteNombre">—</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300" id="modalClienteEmpresa">—</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400" id="modalClienteEmail">—</div>
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                                Nota: si el cliente tiene cotizaciones, el sistema no permitirá eliminarlo.
                            </p>
                        </div>

                        {{-- Footer --}}
                        <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end gap-3">
                            <button type="button" id="closeDeleteModal"
                                    class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                                Cancelar
                            </button>

                            <form id="deleteForm" method="POST" action="#">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold shadow-sm">
                                    Sí, eliminar cliente
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            {{-- JS SIMPLE (sin librerías) --}}
            <script>
                (function () {
                    const modal = document.getElementById('deleteModal');
                    const backdrop = document.getElementById('deleteBackdrop');
                    const closeBtn = document.getElementById('closeDeleteModal');
                    const closeX = document.getElementById('closeDeleteModalX');

                    const form = document.getElementById('deleteForm');
                    const nameEl = document.getElementById('modalClienteNombre');
                    const empresaEl = document.getElementById('modalClienteEmpresa');
                    const emailEl = document.getElementById('modalClienteEmail');

                    function openModal({ action, name, empresa, email }) {
                        form.action = action;

                        nameEl.textContent = name || '—';
                        emailEl.textContent = email || '—';

                        if (empresa && empresa.trim() !== '') {
                            empresaEl.textContent = 'Empresa: ' + empresa;
                            empresaEl.classList.remove('hidden');
                        } else {
                            empresaEl.textContent = '';
                            empresaEl.classList.add('hidden');
                        }

                        modal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    }

                    function closeModal() {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                        form.action = '#';
                    }

                    document.querySelectorAll('[data-open-delete]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            openModal({
                                action: btn.getAttribute('data-delete-action'),
                                name: btn.getAttribute('data-delete-name'),
                                empresa: btn.getAttribute('data-delete-empresa'),
                                email: btn.getAttribute('data-delete-email'),
                            });
                        });
                    });

                    backdrop.addEventListener('click', closeModal);
                    closeBtn.addEventListener('click', closeModal);
                    closeX.addEventListener('click', closeModal);

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                            closeModal();
                        }
                    });
                })();
            </script>

        </div>
    </div>
</x-app-layout>
