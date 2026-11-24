<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(): View
    {
        $categorias = Categoria::withCount('transacciones')
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->paginate(10);

        return view('categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        return view('categorias.create', ['categoria' => new Categoria()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $categoria = Categoria::create($this->validar($request));

        return redirect()->route('categorias.index')
            ->with('success', "Categoría «{$categoria->nombre}» registrada correctamente.");
    }

    public function edit(Categoria $categoria): View
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $data = $this->validar($request);

        // Si ya tiene movimientos, cambiar el tipo rompería la regla ingreso/gasto
        if ($data['tipo'] !== $categoria->tipo && $categoria->transacciones()->exists()) {
            throw ValidationException::withMessages([
                'tipo' => 'No se puede cambiar el tipo porque la categoría ya tiene transacciones registradas.',
            ]);
        }

        $categoria->update($data);

        return redirect()->route('categorias.index')
            ->with('success', "Categoría «{$categoria->nombre}» actualizada correctamente.");
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        if ($categoria->transacciones()->exists()) {
            return redirect()->route('categorias.index')
                ->with('error', "No se puede eliminar la categoría «{$categoria->nombre}» porque tiene transacciones asociadas.");
        }

        try {
            $categoria->delete();
        } catch (QueryException $e) {
            // Respaldo: la llave foránea de la base de datos también lo impide
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene registros relacionados.');
        }

        return redirect()->route('categorias.index')
            ->with('success', "Categoría «{$categoria->nombre}» eliminada correctamente.");
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'tipo' => ['required', Rule::in(['ingreso', 'gasto'])],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
