@if(session('success'))
    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800
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
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800
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
