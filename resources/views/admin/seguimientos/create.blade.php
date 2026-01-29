{{-- resources/views/admin/seguimientos/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                Crear seguimiento · Venta #{{ $venta->id }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Cliente: <b>{{ $venta->usuario->name ?? '—' }}</b>
                @if(!empty($venta->usuario?->empresa)) · {{ $venta->usuario->empresa }} @endif
                @if(!empty($venta->usuario?->email)) · {{ $venta->usuario->email }} @endif
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <b>Salida estimada (ETD)</b>: fecha en que el envío sale del origen ·
                <b>Llegada estimada (ETA)</b>: fecha estimada de llegada al destino.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-900 dark:bg-red-900/30 dark:text-red-100">
                    <div class="font-extrabold">Revisa los campos</div>
                    <ul class="list-disc pl-5 text-sm mt-2">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
                <form method="POST" action="{{ route('admin.ventas.seguimiento.store', $venta->id) }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Proveedor (opcional)</label>
                            <select name="proveedor_id" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">— Sin proveedor —</option>
                                @foreach($proveedores as $p)
                                    <option value="{{ $p->id }}" {{ old('proveedor_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">País destino</label>
                            <input type="text" name="pais_destino" value="{{ old('pais_destino', 'Colombia') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Tipo de envío</label>
                            <select name="tipo_envio" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="maritimo" {{ old('tipo_envio','maritimo') === 'maritimo' ? 'selected' : '' }}>Marítimo</option>
                                <option value="aereo" {{ old('tipo_envio') === 'aereo' ? 'selected' : '' }}>Aéreo</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Incoterm (opcional)</label>
                            <input type="text" name="incoterm" value="{{ old('incoterm') }}"
                                   placeholder="FOB / CIF / EXW..."
                                   class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Estado inicial</label>
                            <select name="estado" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @foreach($estados as $k => $label)
                                    <option value="{{ $k }}" {{ old('estado','ordencompra') === $k ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                Tip: “Compra confirmada” → “En fabricación” → “Despachado” → “En camino” → “Aduana” → “Entregado”.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                    Salida estimada (ETD)
                                </label>
                                <input type="date" name="etd" value="{{ old('etd') }}"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">
                                    Llegada estimada (ETA)
                                </label>
                                <input type="date" name="eta" value="{{ old('eta') }}"
                                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1">Observaciones (opcional)</label>
                        <textarea name="observaciones" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 p-4"
                                  placeholder="Notas internas del proceso...">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <a href="{{ route('admin.ventas.show', $venta->id) }}"
                           class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-extrabold
                                  dark:bg-gray-700/60 dark:hover:bg-gray-700 dark:text-gray-100 text-center">
                            Volver a venta
                        </a>

                        <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold">
                            Crear seguimiento
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
