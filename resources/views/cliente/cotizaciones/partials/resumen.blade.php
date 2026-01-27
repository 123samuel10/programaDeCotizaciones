@php
    $totalPend = $cotizaciones->where('estado','pendiente')->count();
    $totalAcep = $cotizaciones->where('estado','aceptada')->count();
    $totalRech = $cotizaciones->where('estado','rechazada')->count();
    $totalValor = (float) $cotizaciones->sum('total_venta');
@endphp

<div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="text-xs text-gray-500 dark:text-gray-400">Pendientes</div>
        <div class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $totalPend }}</div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="text-xs text-gray-500 dark:text-gray-400">Aceptadas</div>
        <div class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $totalAcep }}</div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="text-xs text-gray-500 dark:text-gray-400">Rechazadas</div>
        <div class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $totalRech }}</div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="text-xs text-gray-500 dark:text-gray-400">Valor total (venta)</div>
        <div class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-gray-100">
            ${{ number_format($totalValor, 2, ',', '.') }}
        </div>
    </div>
</div>
