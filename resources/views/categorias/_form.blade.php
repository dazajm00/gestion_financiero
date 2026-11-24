<x-errores />

<div class="space-y-6">
    <div>
        <x-input-label for="nombre" value="Nombre *" />
        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full"
                      maxlength="150" required autofocus
                      :value="old('nombre', $categoria->nombre)" />
        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
    </div>

    <div>
        <x-input-label for="tipo" value="Tipo *" />
        @php($tipo = old('tipo', $categoria->tipo))
        <select id="tipo" name="tipo" required
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">Seleccione…</option>
            <option value="ingreso" @selected($tipo === 'ingreso')>Ingreso</option>
            <option value="gasto" @selected($tipo === 'gasto')>Gasto</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
    </div>

    <div>
        <x-input-label for="descripcion" value="Descripción" />
        <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full"
                      maxlength="255"
                      :value="old('descripcion', $categoria->descripcion)" />
        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Guardar</x-primary-button>
        <x-link-button variant="secondary" :href="route('categorias.index')">Cancelar</x-link-button>
    </div>
</div>
