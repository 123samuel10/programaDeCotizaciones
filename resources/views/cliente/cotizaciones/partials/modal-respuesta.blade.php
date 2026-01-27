<div id="respuestaModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRespuestaModal()"></div>

    <div class="relative min-h-full flex items-center justify-center p-4">
        <div id="respuestaPanel"
             class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl overflow-hidden
                    transform transition-all duration-200 scale-95 opacity-0">

            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h3 id="respuestaTitle" class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                        —
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Puedes dejar una nota (opcional) para la empresa.
                    </p>
                </div>

                <button type="button"
                        onclick="closeRespuestaModal()"
                        class="shrink-0 w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700
                               flex items-center justify-center text-gray-700 dark:text-gray-200">
                    ✕
                </button>
            </div>

            <form id="respuestaForm" method="POST" action="">
                @csrf
                <div class="p-5 space-y-3">
                    <label class="text-sm font-bold text-gray-900 dark:text-gray-100">Nota (opcional)</label>
                    <textarea name="nota_cliente"
                              rows="4"
                              maxlength="1000"
                              class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800
                                     text-gray-900 dark:text-gray-100 placeholder-gray-400
                                     focus:outline-none focus:ring-2 focus:ring-blue-500 p-4"
                              placeholder="Ej: Por favor confirmar tiempos de entrega / incluir instalación..."></textarea>

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Máximo 1000 caracteres.
                    </div>
                </div>

                <div class="p-5 pt-0 flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button type="button"
                            onclick="closeRespuestaModal()"
                            class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700
                                   text-gray-900 dark:text-gray-100 font-extrabold">
                        Cancelar
                    </button>

                    <button id="respuestaSubmit"
                            type="submit"
                            class="px-5 py-2 rounded-xl font-extrabold text-white">
                        —
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
