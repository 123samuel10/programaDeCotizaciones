<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white leading-tight">
                    Clientes
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Gestión de clientes · Datos para cotizaciones
                </p>
            </div>

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

            {{-- Card principal --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

                {{-- Toolbar (buscar + contador) --}}
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-gray-100 text-gray-700 ring-1 ring-gray-200
                                     dark:bg-gray-700/60 dark:text-gray-200 dark:ring-gray-600">
                            Total: {{ $clientes->count() }}
                        </span>

                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Tip: usa empresa para clientes corporativos.
                        </span>
                    </div>

                    {{-- Búsqueda frontend (no cambia backend) --}}
               <div class="w-full md:w-80">
    <label class="sr-only" for="clienteSearch">Buscar clientes</label>

    <div class="relative">
        {{-- Icono lupa (Heroicons style) --}}
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500"
                 viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M9 3a6 6 0 104.472 10.03l2.249 2.249a.75.75 0 101.06-1.06l-2.249-2.249A6 6 0 009 3zm-4.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z"
                      clip-rule="evenodd" />
            </svg>
        </div>

        <input id="clienteSearch"
               type="text"
               placeholder="Buscar cliente (nombre, empresa, email)..."
               class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700
                      bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                      placeholder:text-gray-400 dark:placeholder:text-gray-500
                      focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
    </div>
</div>

                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="py-3 px-6">Cliente</th>
                                <th class="py-3 px-6">Empresa</th>
                                <th class="py-3 px-6">Email</th>
                                <th class="py-3 px-6 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="clientesTbody">
                            @forelse($clientes as $c)
                                @php
                                    $initial = strtoupper(mb_substr($c->name ?? 'C', 0, 1));
                                @endphp

                                <tr class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50/60 dark:hover:bg-gray-900/30 transition"
                                    data-row-search="{{ strtolower(($c->name ?? '').' '.($c->empresa ?? '').' '.($c->email ?? '')) }}">

                                    {{-- Cliente (avatar + nombre + mini) --}}
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-500/10 ring-1 ring-blue-100 dark:ring-blue-500/20 flex items-center justify-center">
                                                <span class="font-extrabold text-blue-700 dark:text-blue-200">
                                                    {{ $initial }}
                                                </span>
                                            </div>

                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-gray-100 leading-tight">
                                                    {{ $c->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    ID: {{ $c->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Empresa (tag) --}}
                                    <td class="py-4 px-6 text-gray-700 dark:text-gray-200">
                                        @if($c->empresa)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                         bg-gray-100 text-gray-700 ring-1 ring-gray-200
                                                         dark:bg-gray-700/60 dark:text-gray-200 dark:ring-gray-600">
                                                {{ $c->empresa }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    {{-- Email --}}
                                    <td class="py-4 px-6 text-gray-700 dark:text-gray-200 break-words">
                                        {{ $c->email }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.clientes.edit', $c) }}"
                                               class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
                                                      dark:bg-gray-700 dark:hover:bg-gray-600
                                                      text-gray-900 dark:text-gray-100 font-semibold">
                                                Editar
                                            </a>

                                            <button type="button"
                                                    class="px-3 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold shadow-sm"
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
                                    <td colspan="4" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                        No hay clientes aún. Crea el primero con <b>Nuevo cliente</b>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
                    Consejo: Mantén empresa llena si el cliente es corporativo para cotizaciones más limpias.
                </div>
            </div>

            {{-- MODAL PROFESIONAL (UNA SOLA VEZ) --}}
            <div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
                {{-- Backdrop --}}
                <div id="deleteBackdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

                {{-- Dialog --}}
                <div class="relative min-h-screen flex items-center justify-center p-4">
                    <div id="deletePanel"
                         class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden
                                transform transition-all duration-200 scale-95 opacity-0">

                        {{-- Header --}}
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center ring-1 ring-red-100 dark:ring-red-500/20">
                                        <span class="text-red-600 dark:text-red-300 text-xl">⚠️</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
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
                                    <div class="text-gray-900 dark:text-gray-100 font-semibold break-words" id="modalClienteNombre">—</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 break-words" id="modalClienteEmpresa">—</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 break-words" id="modalClienteEmail">—</div>
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

                                <button id="deleteSubmitBtn" type="submit"
                                        class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold shadow-sm">
                                    Sí, eliminar cliente
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            {{-- JS (búsqueda + modal con animación + loading) --}}
            <script>
                (function () {
                    // ===== Buscar =====
                    const search = document.getElementById('clienteSearch');
                    const rows = Array.from(document.querySelectorAll('#clientesTbody tr[data-row-search]'));

                    function normalize(v) { return (v || '').toString().toLowerCase().trim(); }

                    if (search) {
                        search.addEventListener('input', () => {
                            const q = normalize(search.value);
                            rows.forEach(r => {
                                const hay = r.getAttribute('data-row-search') || '';
                                r.classList.toggle('hidden', q && !hay.includes(q));
                            });
                        });
                    }

                    // ===== Modal =====
                    const modal = document.getElementById('deleteModal');
                    const backdrop = document.getElementById('deleteBackdrop');
                    const panel = document.getElementById('deletePanel');
                    const closeBtn = document.getElementById('closeDeleteModal');
                    const closeX = document.getElementById('closeDeleteModalX');

                    const form = document.getElementById('deleteForm');
                    const submitBtn = document.getElementById('deleteSubmitBtn');

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

                        // reset botón
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Sí, eliminar cliente';

                        modal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');

                        requestAnimationFrame(() => {
                            panel.classList.remove('scale-95','opacity-0');
                            panel.classList.add('scale-100','opacity-100');
                        });

                        closeBtn.focus();
                    }

                    function closeModal() {
                        panel.classList.remove('scale-100','opacity-100');
                        panel.classList.add('scale-95','opacity-0');

                        setTimeout(() => {
                            modal.classList.add('hidden');
                            document.body.classList.remove('overflow-hidden');
                            form.action = '#';
                        }, 160);
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
                        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
                    });

                    // loading al enviar
                    form.addEventListener('submit', () => {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Eliminando...';
                    });
                })();
            </script>

        </div>
    </div>
</x-app-layout>
