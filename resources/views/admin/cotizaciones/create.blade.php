<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">Nueva cotización</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border dark:border-gray-700">

                <form method="POST" action="{{ route('admin.cotizaciones.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Cliente</label>
                        <select name="user_id" class="w-full rounded-xl dark:bg-gray-900 dark:text-gray-100">
                            @foreach($clientes as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }} ({{ $cl->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                            Crear cotización
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
