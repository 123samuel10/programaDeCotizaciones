<x-app-layout>
  <x-slot name="header">
    <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white">
      Confirmar acción · Cotización #{{ $cotizacion->id }}
    </h2>
  </x-slot>

  <div class="py-10">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">

        <p class="text-gray-700 dark:text-gray-200">
          Hola <b>{{ $cotizacion->usuario->name }}</b>.
          Vas a <b>{{ $accion }}</b> esta cotización por un enlace del correo.
        </p>

        <div class="mt-4">
          <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
          <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
            ${{ number_format((float)$cotizacion->total_venta, 2, ',', '.') }}
          </div>
        </div>

        <form
          method="POST"
          action="{{ $accion === 'aceptar'
              ? route('public.cotizacion.aceptar.post', ['token' => $cotizacion->token])
              : route('public.cotizacion.rechazar.post', ['token' => $cotizacion->token]) }}"
          class="mt-6 space-y-3"
        >
          @csrf

          <label class="block text-sm text-gray-600 dark:text-gray-300">Nota (opcional)</label>
          <textarea name="nota_cliente" rows="3"
            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            placeholder="Escribe una nota si deseas..."></textarea>

          <div class="flex gap-3 justify-end">
            <a href="{{ route('public.cotizacion.ver', ['token' => $cotizacion->token]) }}"
               class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
              Volver
            </a>

            @if($accion === 'aceptar')
              <button class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-extrabold">
                ✅ Confirmar aceptación
              </button>
            @else
              <button class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold">
                ❌ Confirmar rechazo
              </button>
            @endif
          </div>
        </form>

      </div>
    </div>
  </div>
</x-app-layout>
