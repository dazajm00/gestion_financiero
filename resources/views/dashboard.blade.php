<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inicio</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <!-- Resumen -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <a href="{{ route('categorias.index') }}" class="block bg-white p-6 shadow-sm sm:rounded-lg hover:ring-2 hover:ring-indigo-200">
                    <p class="text-sm text-gray-500">Categorías</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalCategorias }}</p>
                </a>
                <a href="{{ route('cuentas.index') }}" class="block bg-white p-6 shadow-sm sm:rounded-lg hover:ring-2 hover:ring-indigo-200">
                    <p class="text-sm text-gray-500">Cuentas</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $cuentas->count() }}</p>
                </a>
                <a href="{{ route('transacciones.index') }}" class="block bg-white p-6 shadow-sm sm:rounded-lg hover:ring-2 hover:ring-indigo-200">
                    <p class="text-sm text-gray-500">Transacciones</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalTransacciones }}</p>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Saldos -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900">Saldo por cuenta</h3>
                    <ul class="mt-4 divide-y divide-gray-100 text-sm">
                        @forelse ($cuentas as $cuenta)
                            <li class="flex justify-between py-2">
                                <span class="text-gray-700">{{ $cuenta->nombre }}</span>
                                <span class="font-semibold text-gray-900">{{ '$ '.number_format($cuenta->saldo, 2, ',', '.').' '.$cuenta->moneda }}</span>
                            </li>
                        @empty
                            <li class="py-2 text-gray-500">No hay cuentas registradas.</li>
                        @endforelse
                    </ul>
                    @foreach ($totales as $moneda => $total)
                        <div class="mt-4 flex justify-between border-t border-gray-200 pt-4 text-sm">
                            <span class="font-medium text-gray-700">Total disponible ({{ $moneda }})</span>
                            <span class="font-bold text-gray-900">{{ '$ '.number_format($total, 2, ',', '.').' '.$moneda }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Últimos movimientos -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900">Últimos movimientos</h3>
                    <ul class="mt-4 divide-y divide-gray-100 text-sm">
                        @forelse ($ultimas as $transaccion)
                            <li class="flex items-center justify-between gap-4 py-2">
                                <div>
                                    <p class="text-gray-900">{{ $transaccion->categoria->nombre }} · {{ $transaccion->cuenta->nombre }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaccion->fecha->format('d/m/Y') }}</p>
                                </div>
                                <span class="whitespace-nowrap font-semibold {{ $transaccion->tipo === 'ingreso' ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $transaccion->tipo === 'ingreso' ? '+' : '−' }}
                                    {{ '$ '.number_format($transaccion->monto, 2, ',', '.') }}
                                </span>
                            </li>
                        @empty
                            <li class="py-2 text-gray-500">Aún no hay movimientos.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
