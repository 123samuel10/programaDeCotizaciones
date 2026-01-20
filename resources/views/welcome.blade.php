<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Programa de Cotizaciones</title>
    @vite('resources/css/app.css')
</head>
<body class="antialiased bg-gray-100">

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-gray-100 dark:bg-gray-900 selection:bg-blue-500 selection:text-white">

        <!-- Rutas de login/registro -->
        {{-- <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
            @if (Route::has('login'))
                <div class="space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Iniciar Sesión</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Registrarse</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div> --}}

        <!-- Contenido central -->
        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <!-- Título principal -->
            <h1 class="text-5xl font-extrabold text-blue-700 dark:text-blue-400 mb-4 text-center">
                Programa de Cotizaciones
            </h1>

            <!-- Subtítulo -->
            <p class="text-lg text-gray-600 dark:text-gray-300 text-center mb-10">
                Gestiona tus cotizaciones de forma rápida, profesional y segura.
            </p>

            <!-- Opciones principales -->
            <div class="flex justify-center space-x-6">
                <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg transition">
                    Iniciar Sesión
                </a>

                <a href="{{ route('register') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 shadow-lg transition">
                    Registrarse
                </a>
            </div>

            <!-- Información adicional -->
            <div class="mt-16 text-center text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                <p>
                    Bienvenido al sistema de cotizaciones. El administrador puede gestionar cotizaciones y usuarios, mientras que los clientes registrados pueden solicitar cotizaciones y seguir su progreso.
                </p>
            </div>
        </div>

    </div>

</body>
</html>
