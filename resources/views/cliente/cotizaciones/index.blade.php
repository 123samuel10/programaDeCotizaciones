<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white leading-tight">
                    Mis cotizaciones
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Revisa el detalle de tus productos y adiciones, y responde si deseas aceptar o rechazar.
                </p>
            </div>

        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-800 border border-green-200
                            dark:bg-green-900/30 dark:text-green-100 dark:border-green-900">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-800 border border-red-200
                            dark:bg-red-900/30 dark:text-red-100 dark:border-red-900">
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-6">
                @forelse($cotizaciones as $c)

                    @php
                        $estado = $c->estado ?? 'pendiente';
                        $badge = match($estado) {
                            'aceptada' => 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20',
                            'rechazada' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20',
                            default => 'bg-yellow-50 text-yellow-800 ring-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-200 dark:ring-yellow-500/20',
                        };
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl border dark:border-gray-700 overflow-hidden">

                        {{-- Header --}}
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="font-extrabold text-gray-900 dark:text-gray-100 text-lg">
                                        Cotización #{{ $c->id }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Creada: {{ optional($c->created_at)->format('Y-m-d H:i') }}
                                        @if($c->respondida_en)
                                            · Respondida: {{ \Carbon\Carbon::parse($c->respondida_en)->format('Y-m-d H:i') }}
                                        @endif
                                    </div>
                                </div>

                                <span class="text-xs font-extrabold px-3 py-1 rounded-full ring-1 {{ $badge }}">
                                    {{ strtoupper($estado) }}
                                </span>
                            </div>

                            {{-- Resumen --}}
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/30 border dark:border-gray-700">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Productos (líneas)</div>
                                    <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                        {{ $c->items->count() }}
                                    </div>
                                </div>

                                <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/30 border dark:border-gray-700">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Total cotización</div>
                                    <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                        ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Detalle --}}
                        <div class="p-6 space-y-5">

                            @forelse($c->items as $item)
                                @php
                                    $p = $item->producto;

                                    // Totales por línea (solo venta)
                                    $baseVentaLinea = (float)$item->precio_base_venta * (int)$item->cantidad;
                                    $adicVentaLinea = (float)$item->opciones->sum('subtotal_venta');
                                    $totalLinea = $baseVentaLinea + $adicVentaLinea;
                                @endphp

                                <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">

                                    {{-- Encabezado producto --}}
                            {{-- Encabezado producto (foto PRO + zoom) --}}
<div class="p-5 bg-gray-50 dark:bg-gray-900/30">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

        {{-- Izquierda: Foto + info --}}
        <div class="flex items-start gap-4 min-w-0">
            {{-- FOTO (grande y bonita) --}}
            <button type="button"
                    class="shrink-0 group"
                    @if($p && $p->foto)
                        onclick="openProductImageModal('{{ asset('storage/'.$p->foto) }}','{{ e($p->nombre_producto ?? 'Producto') }}','{{ e(($p->marca ?? '').' '.($p->modelo ?? '')) }}')"
                    @else
                        onclick="openProductImageModal('', 'Sin foto', '')"
                    @endif
            >
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
                    @if($p && $p->foto)
                        <img src="{{ asset('storage/'.$p->foto) }}"
                             alt="Foto {{ $p->nombre_producto ?? 'Producto' }}"
                             class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-[1.03]"
                             loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                            Sin foto
                        </div>
                    @endif
                </div>

                <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400 text-center">
                    @if($p && $p->foto) Click para ampliar @endif
                </div>
            </button>

            {{-- INFO --}}
            <div class="min-w-0">
                <div class="font-extrabold text-gray-900 dark:text-gray-100 text-base sm:text-lg break-words">
                    {{ $p->nombre_producto ?? 'Producto' }}
                </div>

                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 break-words">
                    {{ $p->marca ?? '' }} {{ $p->modelo ?? '' }}
                </div>

                {{-- Chips (se ve pro) --}}
                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200">
                        Cantidad: {{ (int)$item->cantidad }}
                    </span>

                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200">
                        Total línea: ${{ number_format($totalLinea, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Derecha: Cantidad (extra) --}}
        <div class="text-right shrink-0">
            <div class="text-xs text-gray-500 dark:text-gray-400">Cantidad</div>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                {{ (int)$item->cantidad }}
            </div>
        </div>

    </div>
</div>


                                    {{-- Total de esa línea (sin mostrar unitarios) --}}
                                    <div class="p-5">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Adiciones (subtotal)</div>
                                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                                    ${{ number_format($adicVentaLinea, 2, ',', '.') }}
                                                </div>
                                            </div>

                                            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Total del producto</div>
                                                <div class="text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                                    ${{ number_format($totalLinea, 2, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Adiciones: mostramos nombre + cantidad + subtotal (sin unitario) --}}
                                        <div class="mt-5">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-extrabold text-gray-900 dark:text-gray-100">
                                                    Adiciones incluidas
                                                </h4>

                                                <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700
                                                             dark:bg-gray-700/60 dark:text-gray-200">
                                                    {{ $item->opciones->count() }} adición(es)
                                                </span>
                                            </div>

                                            @if($item->opciones->count() === 0)
                                                <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                                    Este producto no tiene adiciones.
                                                </div>
                                            @else
                                                <div class="mt-3 overflow-x-auto">
                                                    <table class="min-w-full text-sm">
                                                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                                                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                                                <th class="py-2 px-4">Adición</th>
                                                                <th class="py-2 px-4 text-right">Cant.</th>
                                                                <th class="py-2 px-4 text-right">Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($item->opciones as $io)
                                                                @php
                                                                    $nombreOp = $io->opcion->nombre ?? '—';
                                                                    $cantOp = (int) $io->cantidad;
                                                                    $subOp = (float) $io->subtotal_venta;
                                                                @endphp
                                                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                                                    <td class="py-3 px-4 font-semibold text-gray-900 dark:text-gray-100">
                                                                        {{ $nombreOp }}
                                                                    </td>
                                                                    <td class="py-3 px-4 text-right text-gray-700 dark:text-gray-200">
                                                                        {{ $cantOp }}
                                                                    </td>
                                                                    <td class="py-3 px-4 text-right font-extrabold text-gray-900 dark:text-gray-100">
                                                                        ${{ number_format($subOp, 2, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300">
                                    Esta cotización no tiene productos aún.
                                </div>
                            @endforelse

                            {{-- Total general --}}
                            <div class="mt-6 p-5 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-semibold text-blue-900 dark:text-blue-200">
                                            Total general
                                        </div>
                                        <div class="text-xs text-blue-800/80 dark:text-blue-200/70">
                                            Total final de la cotización.
                                        </div>
                                    </div>

                                    <div class="text-2xl font-extrabold text-blue-900 dark:text-blue-100">
                                        ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Nota del cliente --}}
                            @if($c->nota_cliente)
                                <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                        Nota del cliente
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ $c->nota_cliente }}
                                    </p>
                                </div>
                            @endif

                            {{-- Acciones --}}
                            @if($estado === 'pendiente')
                                <div class="pt-2 flex flex-col sm:flex-row gap-3 sm:justify-end">
                                    <form method="POST" action="{{ route('cliente.cotizaciones.rechazar', $c->id) }}">
                                        @csrf
                                        <button class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold">
                                            Rechazar
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('cliente.cotizaciones.aceptar', $c->id) }}">
                                        @csrf
                                        <button class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-extrabold">
                                            Aceptar
                                        </button>
                                    </form>
                                </div>

                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    * Si quieres, luego agregamos un modal para dejar una nota antes de responder.
                                </p>
                            @endif

                        </div>
                    </div>

                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border dark:border-gray-700 text-gray-600 dark:text-gray-300">
                        No tienes cotizaciones aún.
                    </div>
                @endforelse
            </div>

        </div>
    </div>





    {{-- ✅ MODAL ZOOM IMAGEN PRODUCTO (CLIENTE) --}}
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

            {{-- Header --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h3 id="productImageTitle" class="text-lg font-extrabold text-gray-900 dark:text-gray-100 truncate">
                        —
                    </h3>
                    <p id="productImageSub" class="text-sm text-gray-600 dark:text-gray-300 truncate">
                        —
                    </p>
                </div>

                <button type="button"
                        onclick="closeProductImageModal()"
                        class="shrink-0 w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700
                               flex items-center justify-center text-gray-700 dark:text-gray-200">
                    ✕
                </button>
            </div>

            {{-- Imagen --}}
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

            {{-- Footer --}}
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

<script>
    (function(){
        const modal = document.getElementById('productImageModal');
        const panel = document.getElementById('productImagePanel');
        const img = document.getElementById('productImageTag');
        const title = document.getElementById('productImageTitle');
        const sub = document.getElementById('productImageSub');

        window.openProductImageModal = function(url, t, s){
            title.textContent = t || 'Producto';
            sub.textContent = s || '';
            img.src = url || '';
            img.alt = t || 'Producto';

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(() => {
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            });
        }

        window.closeProductImageModal = function(){
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                img.src = '';
            }, 160);
        }

        document.addEventListener('keydown', (e) => {
            if (modal.classList.contains('hidden')) return;
            if (e.key === 'Escape') window.closeProductImageModal();
        });
    })();
</script>

</x-app-layout>
