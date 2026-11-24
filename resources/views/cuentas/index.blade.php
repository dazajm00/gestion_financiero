<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cuentas</h2>
            <x-link-button :href="route('cuentas.create')">Nueva cuenta</x-link-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Moneda</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Saldo actual</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Movimientos</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($cuentas as $cuenta)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $cuenta->nombre }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $cuenta->descripcion ?: '—' }}</td>
                                    <td class="px-6 py-4 text-center text-gray-600">{{ $cuenta->moneda }}</td>
                                    <td class="px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">
                                        {{ '$ '.number_format($cuenta->saldo, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-600">{{ $cuenta->transacciones_count }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('cuentas.edit', $cuenta) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            <form method="POST" action="{{ route('cuentas.destroy', $cuenta) }}"
                                                  onsubmit="return confirm(@js('¿Eliminar la cuenta «'.$cuenta->nombre.'»?'));">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No hay cuentas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $cuentas->links() }}</div>
        </div>
    </div>
</x-app-layout>
