<x-errores />

@if ($cuentas->isEmpty() || $categorias->isEmpty())
    <div class="mb-6 rounded-md border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
        Para registrar transacciones primero debes crear al menos una
        <a href="{{ route('cuentas.create') }}" class="underline">cuenta</a> y una
        <a href="{{ route('categorias.create') }}" class="underline">categoría</a>.
    </div>
@endif

{{-- Alpine (incluido en Breeze) deshabilita las categorías que no coinciden con el tipo elegido --}}
<div class="space-y-6"
     x-data="{
        tipo: @js(old('tipo', $transaccion->tipo)),
        categoria: @js((string) old('categoria_id', $transaccion->categoria_id)),
        tiposCategoria: @js($categorias->pluck('tipo', 'id')),
        cambiarTipo() {
            if (this.categoria && this.tiposCategoria[this.categoria] !== this.tipo) {
                this.categoria = '';
            }
        }
     }">

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="tipo" value="Tipo *" />
            <select id="tipo" name="tipo" required x-model="tipo" @change="cambiarTipo()"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="ingreso" @selected(old('tipo', $transaccion->tipo) === 'ingreso')>Ingreso</option>
                <option value="gasto" @selected(old('tipo', $transaccion->tipo) === 'gasto')>Gasto</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
        </div>

        <div>
            <x-input-label for="fecha" value="Fecha *" />
            <x-text-input id="fecha" name="fecha" type="date" class="mt-1 block w-full" required
                          :value="old('fecha', optional($transaccion->fecha)->format('Y-m-d'))" />
            <x-input-error class="mt-2" :messages="$errors->get('fecha')" />
        </div>
    </div>

    <div>
        <x-input-label for="cuenta_id" value="Cuenta *" />
        @php($cuentaSeleccionada = (string) old('cuenta_id', $transaccion->cuenta_id))
        <select id="cuenta_id" name="cuenta_id" required
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">Seleccione una cuenta…</option>
            @foreach ($cuentas as $cuenta)
                <option value="{{ $cuenta->id }}" @selected($cuentaSeleccionada === (string) $cuenta->id)>
                    {{ $cuenta->nombre }} — saldo: {{ '$ '.number_format($cuenta->saldo, 2, ',', '.').' '.$cuenta->moneda }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('cuenta_id')" />
    </div>

    <div>
        <x-input-label for="categoria_id" value="Categoría *" />
        <select id="categoria_id" name="categoria_id" required x-model="categoria"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">Seleccione una categoría…</option>
            <optgroup label="Categorías de ingreso">
                @foreach ($categorias->where('tipo', 'ingreso') as $categoria)
                    <option value="{{ $categoria->id }}" :disabled="tipo !== 'ingreso'">{{ $categoria->nombre }}</option>
                @endforeach
            </optgroup>
            <optgroup label="Categorías de gasto">
                @foreach ($categorias->where('tipo', 'gasto') as $categoria)
                    <option value="{{ $categoria->id }}" :disabled="tipo !== 'gasto'">{{ $categoria->nombre }}</option>
                @endforeach
            </optgroup>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('categoria_id')" />
    </div>

    <div>
        <x-input-label for="monto" value="Monto *" />
        <x-text-input id="monto" name="monto" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required
                      placeholder="Ej: 150000"
                      :value="old('monto', $transaccion->monto)" />
        <x-input-error class="mt-2" :messages="$errors->get('monto')" />
    </div>

    <div>
        <x-input-label for="descripcion" value="Descripción" />
        <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full" maxlength="255"
                      :value="old('descripcion', $transaccion->descripcion)" />
        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Guardar</x-primary-button>
        <x-link-button variant="secondary" :href="route('transacciones.index')">Cancelar</x-link-button>
    </div>
</div>
