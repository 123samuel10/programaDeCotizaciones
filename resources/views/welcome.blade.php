<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heral Enterprises | Programa de Cotizaciones</title>
    @vite('resources/css/app.css')
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900">

<div class="relative min-h-screen overflow-hidden bg-gray-100 dark:bg-gray-900">

    <!-- Fondo decorativo -->
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-blue-600/10 blur-3xl"></div>
    </div>

    <!-- Contenido -->
    <div class="relative flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-5xl">

            <!-- Card principal -->
            <div class="rounded-3xl bg-white/80 dark:bg-gray-800/60 backdrop-blur-xl shadow-xl ring-1 ring-gray-200/70 dark:ring-gray-700/60 overflow-hidden">

                <div class="p-8 sm:p-12">

                    <!-- Marca -->
                    <div class="text-center mb-6">
                        <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700
                                     dark:bg-blue-500/10 dark:text-blue-300 ring-1 ring-blue-100 dark:ring-blue-500/20">
                            Heral Enterprises
                        </span>
                    </div>

                    <!-- Título -->
                    <div class="text-center">
                        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                            Programa de Cotizaciones
                        </h1>

                        <p class="mt-4 text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                            Plataforma profesional de <strong>Heral Enterprises</strong> para la gestión de
                            cotizaciones, productos, costos y ventas de forma clara y segura.
                        </p>
                    </div>

                    <!-- Botones -->
                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('login') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-6 py-3 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg hover:from-blue-700 hover:to-blue-800 transition">
                            Iniciar Sesión
                        </a>

                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-6 py-3 font-semibold
                                  text-gray-800 bg-gray-100 ring-1 ring-gray-200 hover:bg-gray-200 transition
                                  dark:text-gray-100 dark:bg-gray-700/60 dark:ring-gray-600 dark:hover:bg-gray-700">
                            Registrarse
                        </a>
                    </div>

                    <!-- Beneficios -->
                    <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-2xl bg-white/70 dark:bg-gray-900/30 p-5 ring-1 ring-gray-200/70 dark:ring-gray-700/60">
                            <h3 class="font-bold text-gray-900 dark:text-white">Cotizaciones profesionales</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Totales claros de venta y costo, con adiciones por producto.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/70 dark:bg-gray-900/30 p-5 ring-1 ring-gray-200/70 dark:ring-gray-700/60">
                            <h3 class="font-bold text-gray-900 dark:text-white">Gestión centralizada</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Administra productos, clientes y cotizaciones desde un solo lugar.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/70 dark:bg-gray-900/30 p-5 ring-1 ring-gray-200/70 dark:ring-gray-700/60">
                            <h3 class="font-bold text-gray-900 dark:text-white">Acceso seguro</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Roles definidos para administradores y clientes.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="px-8 sm:px-12 py-4 bg-gray-50/70 dark:bg-gray-900/30 border-t border-gray-200/60 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>© {{ date('Y') }} Heral Enterprises</span>
                        <span>Programa de Cotizaciones</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
