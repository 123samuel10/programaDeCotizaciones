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
        <div class="w-full max-w-6xl">

            <!-- Card principal -->
            <div class="rounded-3xl bg-white/80 dark:bg-gray-800/60 backdrop-blur-xl shadow-xl
                        ring-1 ring-gray-200/70 dark:ring-gray-700/60 overflow-hidden">

                <div class="p-10 sm:p-14">

                    <!-- ================= MARCA ================= -->
                    <div class="text-center mb-12">

                        <!-- Logo horizontal grande -->
                        <div class="flex justify-center mb-6">
                            <img src="{{ asset('images/Logo_Heral.jpeg') }}"
                                 alt="Logo Heral Enterprises"
                                 class="max-w-[320px] sm:max-w-[420px] md:max-w-[480px]
                                        h-auto object-contain drop-shadow-xl"
                                 loading="lazy">
                        </div>

                        <!-- Submarca -->
                        <span class="inline-flex items-center rounded-full
                                     bg-blue-50 px-5 py-2 text-sm font-semibold text-blue-700
                                     ring-1 ring-blue-100
                                     dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/20">
                            Plataforma de gestión de cotizaciones, productos y ventas
                        </span>
                    </div>

                    <!-- ================= TÍTULO ================= -->
                    <div class="text-center max-w-3xl mx-auto">
                     <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight
           text-gray-900 dark:text-white">
     Heral Enterprises
</h1>

                        <p class="mt-6 text-base sm:text-lg text-gray-600 dark:text-gray-300">
                            Plataforma profesional para la gestión de
                            <strong>cotizaciones</strong>, <strong>productos</strong>,
                            <strong>costos</strong> y <strong>ventas</strong>,
                            diseñada para trabajar de forma clara, segura y eficiente.
                        </p>
                    </div>

                    <!-- ================= BOTONES ================= -->
                    <div class="mt-14 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('login') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center
                                  rounded-xl px-8 py-3 font-bold text-white
                                  bg-gradient-to-r from-blue-600 to-blue-700
                                  shadow-lg hover:from-blue-700 hover:to-blue-800 transition">
                            Iniciar sesión
                        </a>

                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center
                                  rounded-xl px-8 py-3 font-bold
                                  text-gray-800 bg-gray-100 ring-1 ring-gray-200
                                  hover:bg-gray-200 transition
                                  dark:text-gray-100 dark:bg-gray-700/60
                                  dark:ring-gray-600 dark:hover:bg-gray-700">
                            Registrarse
                        </a>
                    </div>

                    <!-- ================= BENEFICIOS ================= -->
                    <div class="mt-16 grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="rounded-2xl bg-white/70 dark:bg-gray-900/30 p-6
                                    ring-1 ring-gray-200/70 dark:ring-gray-700/60">
                            <h3 class="font-bold text-gray-900 dark:text-white">
                                Cotizaciones profesionales
                            </h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                Totales claros de venta y costo, con adiciones por producto.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/70 dark:bg-gray-900/30 p-6
                                    ring-1 ring-gray-200/70 dark:ring-gray-700/60">
                            <h3 class="font-bold text-gray-900 dark:text-white">
                                Gestión centralizada
                            </h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                Administra productos, clientes y cotizaciones desde un solo lugar.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/70 dark:bg-gray-900/30 p-6
                                    ring-1 ring-gray-200/70 dark:ring-gray-700/60">
                            <h3 class="font-bold text-gray-900 dark:text-white">
                                Acceso seguro
                            </h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                Roles definidos para administradores y clientes.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- ================= FOOTER ================= -->
                <div class="px-10 sm:px-14 py-4 bg-gray-50/70 dark:bg-gray-900/30
                            border-t border-gray-200/60 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-2
                                text-xs text-gray-500 dark:text-gray-400">
                        <span>© {{ date('Y') }} Heral Enterprises</span>
                        <span>Plataforma de gestión de cotizaciones, productos y ventas</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
