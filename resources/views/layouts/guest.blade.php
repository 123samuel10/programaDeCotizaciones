<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Heral Enterprises | Acceso</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
<div class="min-h-screen flex flex-col justify-center items-center px-6 py-10">

    <a href="/" class="mb-6 text-center">
        <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
            Heral <span class="text-blue-600 dark:text-blue-400">Enterprises</span>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Programa de Cotizaciones
        </div>
    </a>

    <div class="w-full sm:max-w-md rounded-2xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200/70 dark:ring-gray-700/60 p-6">
        {{ $slot }}
    </div>

</div>
</body>
</html>
