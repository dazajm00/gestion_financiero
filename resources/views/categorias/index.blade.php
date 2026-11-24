<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categorías</h2>
            <x-link-button :href="route('categorias.create')">Nueva categoría</x-link-button>
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
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Transacciones</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($categorias as $categoria)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $categoria->nombre }}</td>
                                    <td class="px-6 py-4"><x-tipo-badge :tipo="$categoria->tipo" /></td>
                                    <td class="px-6 py-4 text-gray-600">{{ $categoria->descripcion ?: '—' }}</td>
                                    <td class="px-6 py-4 text-center text-gray-600">{{ $categoria->transacciones_count }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('categorias.edit', $categoria) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            <form method="POST" action="{{ route('categorias.destroy', $categoria) }}"
                                                  onsubmit="return confirm(@js('¿Eliminar la categoría «'.$categoria->nombre.'»?'));">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No hay categorías registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $categorias->links() }}</div>
        </div>
    </div>
</x-app-layout>
