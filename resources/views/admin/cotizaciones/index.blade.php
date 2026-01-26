<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
                Cotizaciones
            </h2>

            <a href="{{ route('admin.cotizaciones.create') }}"
               class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                + Nueva cotización
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

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="py-2">#</th>
                                <th class="py-2">Cliente</th>
                                <th class="py-2">Estado</th>
                                <th class="py-2">Respondida</th>
                                <th class="py-2 text-center">Líneas</th>
                                <th class="py-2 text-right">Total Venta</th>
                                <th class="py-2 text-right">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($cotizaciones as $c)
                                @php
                                    $estado = $c->estado ?? 'pendiente';
                                    $badge = match($estado) {
                                        'aceptada' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                                        'rechazada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                                        default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                                    };
                                @endphp

                                <tr class="border-t dark:border-gray-700 align-top">
                                    <td class="py-3 font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $c->id }}
                                    </td>

                                    <td class="py-3">
                                        <div class="text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ $c->usuario->name ?? '—' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $c->usuario->email ?? '' }}
                                        </div>

                                        @if($c->nota_cliente)
                                            <div class="mt-2 text-xs p-2 rounded-xl bg-gray-50 dark:bg-gray-900/30 border dark:border-gray-700 text-gray-700 dark:text-gray-200">
                                                <span class="font-semibold">Nota:</span> {{ $c->nota_cliente }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3">
                                        <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 inline-flex {{ $badge }}">
                                            {{ strtoupper($estado) }}
                                        </span>
                                    </td>

                                    <td class="py-3 text-gray-700 dark:text-gray-200">
                                        @if($c->respondida_en)
                                            {{ \Carbon\Carbon::parse($c->respondida_en)->format('Y-m-d H:i') }}
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td class="py-3 text-center">
                                        {{ $c->items_count ?? $c->items()->count() }}
                                    </td>

                                    <td class="py-3 text-right text-gray-900 dark:text-gray-100">
                                        ${{ number_format((float) $c->total_venta, 2, ',', '.') }}
                                    </td>

                                    <td class="py-3 text-right">
                                        <div class="inline-flex items-center gap-3">
                                            <a class="text-blue-600 hover:underline font-semibold"
                                               href="{{ route('admin.cotizaciones.edit', $c->id) }}">
                                                Abrir
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('admin.cotizaciones.destroy', $c->id) }}"
                                                  class="inline js-delete-quote-form"
                                                  data-id="{{ $c->id }}"
                                                  data-cliente="{{ $c->usuario->name ?? '—' }}"
                                                  data-email="{{ $c->usuario->email ?? '' }}"
                                                  data-lineas="{{ $c->items_count ?? $c->items()->count() }}">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                        class="text-red-600 hover:underline font-semibold js-open-delete-quote">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t dark:border-gray-700">
                                    <td colspan="7" class="py-8 text-center text-gray-600 dark:text-gray-300">
                                        No hay cotizaciones aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    {{-- ✅ MODAL ELIMINAR COTIZACIÓN --}}
    <div id="deleteQuoteModal"
         class="fixed inset-0 z-50 hidden"
         aria-labelledby="deleteQuoteModalTitle"
         aria-modal="true"
         role="dialog">

        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="deleteQuoteBackdrop"></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden
                        transform transition-all duration-200 scale-95 opacity-0"
                 id="deleteQuotePanel">

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-11 h-11 rounded-2xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center ring-1 ring-red-100 dark:ring-red-500/20">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 9v4m0 4h.01M10 3h4l1 2h4v2H5V5h4l1-2Zm-3 6h10l-1 12H9L8 9Z"
                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <div class="flex-1">
                            <h3 id="deleteQuoteModalTitle" class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                Eliminar cotización
                            </h3>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Vas a eliminar la cotización <span class="font-semibold text-gray-900 dark:text-gray-100" id="dqId">#—</span>.
                                Esta acción no se puede deshacer.
                            </p>

                            <div class="mt-3 text-xs rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700 p-3">
                                <div class="font-semibold text-gray-700 dark:text-gray-200" id="dqCliente">—</div>
                                <div class="text-gray-500 dark:text-gray-300 mt-0.5" id="dqEmail">—</div>
                                <div class="text-gray-500 dark:text-gray-300 mt-1">
                                    Líneas: <span class="font-semibold text-gray-700 dark:text-gray-200" id="dqLineas">—</span>
                                </div>
                            </div>

                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                Se eliminarán también todas las líneas y adiciones asociadas.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-6 flex gap-3">
                    <button type="button"
                            class="w-full px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
                                   dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold"
                            id="dqCancel">
                        Cancelar
                    </button>

                    <button type="button"
                            class="w-full px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold"
                            id="dqConfirm">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ SCRIPT MODAL --}}
    <script>
    (function () {
        const modal = document.getElementById('deleteQuoteModal');
        const panel = document.getElementById('deleteQuotePanel');
        const backdrop = document.getElementById('deleteQuoteBackdrop');

        const dqId = document.getElementById('dqId');
        const dqCliente = document.getElementById('dqCliente');
        const dqEmail = document.getElementById('dqEmail');
        const dqLineas = document.getElementById('dqLineas');

        const cancelBtn = document.getElementById('dqCancel');
        const confirmBtn = document.getElementById('dqConfirm');

        let currentForm = null;

        function openModal(form) {
            currentForm = form;

            dqId.textContent = '#' + (form.dataset.id || '—');
            dqCliente.textContent = form.dataset.cliente || '—';
            dqEmail.textContent = form.dataset.email || '';
            dqLineas.textContent = form.dataset.lineas || '0';

            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Sí, eliminar';

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(() => {
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            });

            cancelBtn.focus();
        }

        function closeModal() {
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                currentForm = null;
            }, 160);
        }

        document.querySelectorAll('.js-open-delete-quote').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const form = e.currentTarget.closest('form');
                openModal(form);
            });
        });

        cancelBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);

        confirmBtn.addEventListener('click', () => {
            if (!currentForm) return;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Eliminando...';
            currentForm.submit();
        });

        document.addEventListener('keydown', (e) => {
            if (modal.classList.contains('hidden')) return;
            if (e.key === 'Escape') closeModal();
        });
    })();
    </script>
</x-app-layout>
