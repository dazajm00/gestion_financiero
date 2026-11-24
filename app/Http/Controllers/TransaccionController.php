<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Transaccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransaccionController extends Controller
{
    public function index(): View
    {
        $transacciones = Transaccion::with(['cuenta', 'categoria'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10);

        return view('transacciones.index', compact('transacciones'));
    }

    public function create(): View
    {
        return view('transacciones.create', [
            'transaccion' => new Transaccion(['tipo' => 'ingreso', 'fecha' => now()->toDateString()]),
        ] + $this->opciones());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);

        DB::transaction(function () use ($data, $request) {
            // Ingreso: suma al saldo. Gasto: resta (si no supera el saldo disponible).
            $this->actualizarSaldos(null, $data);

            Transaccion::create($data + [
                'creado_por' => mb_substr($request->user()->email, 0, 150),
            ]);
        });

        $cuenta = Cuenta::find($data['cuenta_id']);
        $tipo = $data['tipo'] === 'ingreso' ? 'Ingreso' : 'Gasto';

        return redirect()->route('transacciones.index')->with('success',
            "{$tipo} registrado. Nuevo saldo de «{$cuenta->nombre}»: $ ".number_format($cuenta->saldo, 2, ',', '.').' '.$cuenta->moneda.'.'
        );
    }

    public function edit(Transaccion $transaccion): View
    {
        return view('transacciones.edit', ['transaccion' => $transaccion] + $this->opciones());
    }

    public function update(Request $request, Transaccion $transaccion): RedirectResponse
    {
        $data = $this->validar($request);

        DB::transaction(function () use ($transaccion, $data) {
            $actual = Transaccion::lockForUpdate()->findOrFail($transaccion->id);

            // Revierte el efecto anterior y aplica el nuevo (aunque cambie de cuenta o de tipo)
            $this->actualizarSaldos($actual, $data);

            $actual->update($data);
        });

        $cuenta = Cuenta::find($data['cuenta_id']);

        return redirect()->route('transacciones.index')->with('success',
            'Transacción actualizada. Saldo de «'.$cuenta->nombre.'»: $ '.number_format($cuenta->saldo, 2, ',', '.').' '.$cuenta->moneda.'.'
        );
    }

    public function destroy(Transaccion $transaccion): RedirectResponse
    {
        try {
            DB::transaction(function () use ($transaccion) {
                $actual = Transaccion::lockForUpdate()->findOrFail($transaccion->id);

                // Revierte el efecto de la transacción sobre el saldo
                $this->actualizarSaldos($actual, null);

                $actual->delete();
            });
        } catch (ValidationException $e) {
            return redirect()->route('transacciones.index')
                ->with('error', collect($e->errors())->flatten()->first());
        }

        $cuenta = Cuenta::find($transaccion->cuenta_id);

        return redirect()->route('transacciones.index')->with('success',
            'Transacción eliminada. Saldo de «'.$cuenta->nombre.'»: $ '.number_format($cuenta->saldo, 2, ',', '.').' '.$cuenta->moneda.'.'
        );
    }

    /**
     * Revierte el efecto de $anterior (si existe) y aplica el de $nueva (si existe).
     * Trabaja en centavos (enteros) para evitar errores de redondeo y nunca deja
     * una cuenta con saldo negativo.
     */
    private function actualizarSaldos(?Transaccion $anterior, ?array $nueva): void
    {
        $ids = array_filter([$anterior?->cuenta_id, $nueva['cuenta_id'] ?? null]);
        $ids = array_values(array_unique(array_map('intval', $ids)));

        // Bloquea las filas de las cuentas involucradas mientras dura la operación
        $cuentas = Cuenta::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');

        $saldos = [];
        foreach ($cuentas as $id => $cuenta) {
            $saldos[$id] = $this->centavos($cuenta->saldo);
        }

        // 1. Revertir el efecto anterior
        if ($anterior) {
            $saldos[(int) $anterior->cuenta_id] -= $this->efecto($anterior->tipo, $anterior->monto);
        }

        // 2. Aplicar el efecto nuevo
        if ($nueva) {
            $id = (int) $nueva['cuenta_id'];
            $disponible = $saldos[$id];
            $saldos[$id] += $this->efecto($nueva['tipo'], $nueva['monto']);

            if ($nueva['tipo'] === 'gasto' && $saldos[$id] < 0) {
                $cuenta = $cuentas[$id];
                throw ValidationException::withMessages([
                    'monto' => "El gasto supera el saldo disponible de la cuenta «{$cuenta->nombre}» (disponible: "
                        .'$ '.number_format($disponible / 100, 2, ',', '.').' '.$cuenta->moneda.').',
                ]);
            }
        }

        // 3. Ninguna cuenta puede quedar en negativo (p. ej. al eliminar un ingreso ya gastado)
        foreach ($saldos as $id => $saldo) {
            if ($saldo < 0) {
                $cuenta = $cuentas[$id];
                throw ValidationException::withMessages([
                    'cuenta_id' => "No se puede realizar la operación: la cuenta «{$cuenta->nombre}» quedaría con saldo negativo ("
                        .'$ '.number_format($saldo / 100, 2, ',', '.').' '.$cuenta->moneda.').',
                ]);
            }
        }

        // 4. Guardar los nuevos saldos
        foreach ($cuentas as $id => $cuenta) {
            $cuenta->saldo = number_format($saldos[$id] / 100, 2, '.', '');
            $cuenta->save();
        }
    }

    private function efecto(string $tipo, $monto): int
    {
        $centavos = $this->centavos($monto);

        return $tipo === 'ingreso' ? $centavos : -$centavos;
    }

    private function centavos($valor): int
    {
        return (int) round(((float) $valor) * 100);
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'cuenta_id' => ['required', 'integer', 'exists:cuentas,id'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'tipo' => ['required', Rule::in(['ingreso', 'gasto'])],
            'monto' => ['required', 'numeric', 'min:0.01', 'decimal:0,2', 'max:9999999999999999.99'],
            'fecha' => ['required', 'date'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], [
            'monto.min' => 'El monto debe ser mayor que cero.',
        ]);

        // Categoría de ingreso solo para ingresos; de gasto solo para gastos
        $categoria = Categoria::findOrFail($data['categoria_id']);

        if ($categoria->tipo !== $data['tipo']) {
            throw ValidationException::withMessages([
                'categoria_id' => "La categoría «{$categoria->nombre}» es de tipo {$categoria->tipo}; "
                    ."solo puede usarse en transacciones de tipo {$categoria->tipo}.",
            ]);
        }

        return $data;
    }

    private function opciones(): array
    {
        return [
            'cuentas' => Cuenta::orderBy('nombre')->get(),
            'categorias' => Categoria::orderBy('tipo')->orderBy('nombre')->get(),
        ];
    }
}
