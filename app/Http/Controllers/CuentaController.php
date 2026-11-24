<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CuentaController extends Controller
{
    public function index(): View
    {
        $cuentas = Cuenta::withCount('transacciones')
            ->orderBy('nombre')
            ->paginate(10);

        return view('cuentas.index', compact('cuentas'));
    }

    public function create(): View
    {
        return view('cuentas.create', [
            'cuenta' => new Cuenta(['saldo' => 0, 'moneda' => 'COP']),
            'tieneMovimientos' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
{
    // Quita los puntos de miles de la máscara: "200.000" -> "200000"
    $request->merge(['saldo' => str_replace('.', '', (string) $request->input('saldo'))]);

    $data = $request->validate($this->reglas(conSaldo: true));
    $data['moneda'] = strtoupper(trim($data['moneda']));

    $cuenta = Cuenta::create($data);

    return redirect()->route('cuentas.index')
        ->with('success', "Cuenta «{$cuenta->nombre}» registrada correctamente.");
}

    public function edit(Cuenta $cuenta): View
    {
        return view('cuentas.edit', [
            'cuenta' => $cuenta,
            'tieneMovimientos' => $cuenta->transacciones()->exists(),
        ]);
    }

    public function update(Request $request, Cuenta $cuenta): RedirectResponse
    {
        // Si la cuenta ya tiene movimientos, el saldo solo cambia mediante transacciones
        $tieneMovimientos = $cuenta->transacciones()->exists();

        $data = $request->validate($this->reglas(conSaldo: ! $tieneMovimientos));
        $data['moneda'] = strtoupper(trim($data['moneda']));

        $cuenta->update($data);

        return redirect()->route('cuentas.index')
            ->with('success', "Cuenta «{$cuenta->nombre}» actualizada correctamente.");
    }

    public function destroy(Cuenta $cuenta): RedirectResponse
    {
        if ($cuenta->transacciones()->exists()) {
            return redirect()->route('cuentas.index')
                ->with('error', "No se puede eliminar la cuenta «{$cuenta->nombre}» porque tiene movimientos asociados.");
        }

        try {
            $cuenta->delete();
        } catch (QueryException $e) {
            return redirect()->route('cuentas.index')
                ->with('error', 'No se puede eliminar la cuenta porque tiene registros relacionados.');
        }

        return redirect()->route('cuentas.index')
            ->with('success', "Cuenta «{$cuenta->nombre}» eliminada correctamente.");
    }

    private function reglas(bool $conSaldo): array
    {
        $reglas = [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'moneda' => ['required', 'string', 'max:10'],
        ];

        if ($conSaldo) {
            $reglas['saldo'] = ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999999999.99'];
        }

        return $reglas;
    }
}
