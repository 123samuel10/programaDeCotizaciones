<div id="productImageModal"
     class="fixed inset-0 z-50 hidden"
     aria-labelledby="productImageTitle"
     aria-modal="true"
     role="dialog">

    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeProductImageModal()"></div>

    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-3xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl overflow-hidden
                    transform transition-all duration-200 scale-95 opacity-0"
             id="productImagePanel">

            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h3 id="productImageTitle" class="text-lg font-extrabold text-gray-900 dark:text-gray-100 truncate">—</h3>
                    <p id="productImageSub" class="text-sm text-gray-600 dark:text-gray-300 truncate">—</p>
                </div>

                <button type="button"
                        onclick="closeProductImageModal()"
                        class="shrink-0 w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700
                               flex items-center justify-center text-gray-700 dark:text-gray-200">
                    ✕
                </button>
            </div>

            <div class="p-5">
                <div class="rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 bg-black/5 dark:bg-white/5">
                    <img id="productImageTag"
                         src=""
                         alt=""
                         class="w-full max-h-[70vh] object-contain bg-white dark:bg-gray-900">
                </div>

                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    Tip: si estás en celular, puedes hacer zoom con los dedos.
                </p>
            </div>

            <div class="p-5 pt-0 flex justify-end">
                <button type="button"
                        onclick="closeProductImageModal()"
                        class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
