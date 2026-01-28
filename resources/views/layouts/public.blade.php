<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Cotización' }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="max-w-4xl mx-auto p-6">
        {{ $slot }}
    </div>
</body>
</html>
