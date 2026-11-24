<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar transacción</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <p class="mb-6 text-sm text-gray-600">
                    Al guardar, se revierte el efecto anterior de esta transacción sobre el saldo y se aplica el nuevo valor.
                    Registrada por: <span class="font-medium">{{ $transaccion->creado_por ?: '—' }}</span>
                </p>

                <form method="POST" action="{{ route('transacciones.update', $transaccion) }}">
                    @csrf
                    @method('PUT')
                    @include('transacciones._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
