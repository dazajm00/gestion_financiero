<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transacciones</h2>
            <x-link-button :href="route('transacciones.create')">Nueva transacción</x-link-button>
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
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Cuenta</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Registrado por</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($transacciones as $transaccion)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-700">{{ $transaccion->fecha->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4"><x-tipo-badge :tipo="$transaccion->tipo" /></td>
                                    <td class="px-4 py-4 text-gray-900">{{ $transaccion->cuenta->nombre }}</td>
                                    <td class="px-4 py-4 text-gray-900">{{ $transaccion->categoria->nombre }}</td>
                                    <td class="px-4 py-4 text-gray-600">{{ $transaccion->descripcion ?: '—' }}</td>
                                    <td class="px-4 py-4 text-right font-semibold whitespace-nowrap {{ $transaccion->tipo === 'ingreso' ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $transaccion->tipo === 'ingreso' ? '+' : '−' }}
                                        {{ '$ '.number_format($transaccion->monto, 2, ',', '.').' '.$transaccion->cuenta->moneda }}
                                    </td>
                                    <td class="px-4 py-4 text-gray-600">{{ $transaccion->creado_por ?: '—' }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('transacciones.edit', $transaccion) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            <form method="POST" action="{{ route('transacciones.destroy', $transaccion) }}"
                                                  onsubmit="return confirm('¿Eliminar esta transacción? Su efecto sobre el saldo será revertido.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No hay transacciones registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $transacciones->links() }}</div>
        </div>
    </div>
</x-app-layout>
