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

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($productos as $producto)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">

                        {{-- FOTO --}}
                        <div class="mb-4">
                            <div class="w-full h-40 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                @if($producto->foto)
                                    <img src="{{ asset('storage/'.$producto->foto) }}"
                                         class="w-full h-full object-cover"
                                         alt="Foto {{ $producto->nombre_producto }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-sm text-gray-400">
                                        Sin foto
                                    </div>
                                @endif
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 break-words">
                            {{ $producto->nombre_producto }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 break-words">
                            {{ $producto->marca }} · {{ $producto->modelo }}
                        </p>

                        <div class="mt-4 text-sm text-gray-700 dark:text-gray-200 space-y-1">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-600 dark:text-gray-300">Base Venta (EXWORKS)</span>
                                <span class="font-semibold whitespace-nowrap">
                                    ${{ number_format($producto->precio_base_venta, 2, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-600 dark:text-gray-300">Base Costo</span>
                                <span class="font-semibold whitespace-nowrap">
                                    ${{ number_format($producto->precio_base_costo, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-5 flex gap-2">
                            <a href="{{ route('admin.productos.edit', $producto) }}"
                               class="inline-flex w-full justify-center px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold">
                                Abrir
                            </a>

                            {{-- eliminar con modal --}}
                            <form method="POST"
                                  action="{{ route('admin.productos.destroy', $producto) }}"
                                  class="w-full js-delete-form"
                                  data-producto="{{ $producto->nombre_producto }}"
                                  data-subtitle="{{ $producto->marca }} · {{ $producto->modelo }}">
                                @csrf
                                @method('DELETE')

                                <button type="button"
                                        class="w-full px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold js-open-delete">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-700 dark:text-gray-200">
                        No hay productos aún. Crea el primero con el botón <b>Nuevo producto</b>.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- Modal eliminar producto --}}
    <div id="deleteModal"
         class="fixed inset-0 z-50 hidden"
         aria-labelledby="deleteModalTitle"
         aria-modal="true"
         role="dialog">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="deleteBackdrop"></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden
                        transform transition-all duration-200 scale-95 opacity-0"
                 id="deleteModalPanel">

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-11 h-11 rounded-2xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center ring-1 ring-red-100 dark:ring-red-500/20">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 9v4m0 4h.01M10 3h4l1 2h4v2H5V5h4l1-2Zm-3 6h10l-1 12H9L8 9Z"
                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <div class="flex-1">
                            <h3 id="deleteModalTitle" class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                Eliminar producto
                            </h3>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Vas a eliminar <span class="font-semibold text-gray-900 dark:text-gray-100 break-words" id="deleteProductoNombre">—</span>.
                                Esta acción no se puede deshacer.
                            </p>

                            <div class="mt-3 text-xs rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700 p-3">
                                <div class="font-semibold text-gray-700 dark:text-gray-200 break-words" id="deleteProductoNombreMini">—</div>
                                <div class="text-gray-500 dark:text-gray-300 mt-0.5 break-words" id="deleteProductoSub">—</div>
                            </div>

                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                Si el producto está usado en cotizaciones, el sistema no lo permitirá.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-6 flex gap-3">
                    <button type="button"
                            class="w-full px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
                                   dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold"
                            id="deleteCancelBtn">
                        Cancelar
                    </button>

                    <button type="button"
                            class="w-full px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold"
                            id="deleteConfirmBtn">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const modal = document.getElementById('deleteModal');
        const panel = document.getElementById('deleteModalPanel');
        const backdrop = document.getElementById('deleteBackdrop');

        const cancelBtn = document.getElementById('deleteCancelBtn');
        const confirmBtn = document.getElementById('deleteConfirmBtn');

        const nombre = document.getElementById('deleteProductoNombre');
        const nombreMini = document.getElementById('deleteProductoNombreMini');
        const sub = document.getElementById('deleteProductoSub');

        let currentForm = null;

        function openModal(form) {
            currentForm = form;

            nombre.textContent = form.dataset.producto || 'este producto';
            nombreMini.textContent = form.dataset.producto || 'este producto';
            sub.textContent = form.dataset.subtitle || '';

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

        document.querySelectorAll('.js-delete-form .js-open-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                openModal(e.currentTarget.closest('form'));
            });
        });

        cancelBtn.addEventListener('click', closeModal);

        confirmBtn.addEventListener('click', () => {
            if (!currentForm) return;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Eliminando...';
            currentForm.submit();
        });

        backdrop.addEventListener('click', closeModal);

        document.addEventListener('keydown', (e) => {
            if (modal.classList.contains('hidden')) return;
            if (e.key === 'Escape') closeModal();
        });
    })();
    </script>
</x-app-layout>
