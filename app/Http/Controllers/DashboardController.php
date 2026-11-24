<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Transaccion;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $cuentas = Cuenta::orderBy('nombre')->get();

        return view('dashboard', [
            'cuentas' => $cuentas,
            'totales' => $cuentas->groupBy('moneda')->map(fn ($grupo) => $grupo->sum(fn ($c) => (float) $c->saldo)),
            'totalCategorias' => Categoria::count(),
            'totalTransacciones' => Transaccion::count(),
            'ultimas' => Transaccion::with(['cuenta', 'categoria'])
                ->orderByDesc('fecha')->orderByDesc('id')->take(5)->get(),
        ]);
    }
}
