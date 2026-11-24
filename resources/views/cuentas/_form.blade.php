<x-errores />

<div class="space-y-6">
    <div>
        <x-input-label for="nombre" value="Nombre *" />
        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full"
                      maxlength="150" required autofocus
                      :value="old('nombre', $cuenta->nombre)" />
        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
    </div>

    <div>
        <x-input-label for="descripcion" value="Descripción" />
        <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full"
                      maxlength="255"
                      :value="old('descripcion', $cuenta->descripcion)" />
        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            @if ($tieneMovimientos)
                <x-input-label for="saldo" value="Saldo actual" />
                <x-text-input id="saldo" type="text" class="mt-1 block w-full bg-gray-100 text-gray-600"
                              disabled
                              :value="'$ '.number_format($cuenta->saldo, 2, ',', '.')" />
                <p class="mt-2 text-xs text-gray-500">
                    La cuenta tiene movimientos: el saldo se actualiza automáticamente con las transacciones.
                </p>
            @else
                <x-input-label for="saldo" value="Saldo *" />
                <x-text-input id="saldo" name="saldo" type="number" step="0.01" min="0" class="mt-1 block w-full"
                              required
                              :value="old('saldo', $cuenta->saldo)" />
                <x-input-error class="mt-2" :messages="$errors->get('saldo')" />
            @endif
        </div>

        <div>
            <x-input-label for="moneda" value="Moneda *" />
            <x-text-input id="moneda" name="moneda" type="text" class="mt-1 block w-full uppercase"
                          maxlength="10" required
                          :value="old('moneda', $cuenta->moneda ?? 'COP')" />
            <x-input-error class="mt-2" :messages="$errors->get('moneda')" />
        </div>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Guardar</x-primary-button>
        <x-link-button variant="secondary" :href="route('cuentas.index')">Cancelar</x-link-button>
    </div>
</div>
