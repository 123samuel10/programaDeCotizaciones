@php
    $total = $cotizaciones->count();
@endphp

<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
            Mis cotizaciones
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
            Revisa el detalle de tus productos y adiciones. Puedes aceptar o rechazar una cotización cuando esté pendiente.
        </p>
    </div>

    <div class="flex flex-wrap gap-2">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-extrabold ring-1
            bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 8v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
            </svg>
            Pendientes: {{ (int)($pendientes ?? 0) }}
        </span>

        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-extrabold ring-1
            bg-gray-50 text-gray-700 ring-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Total: {{ $total }}
        </span>
    </div>
</div>
