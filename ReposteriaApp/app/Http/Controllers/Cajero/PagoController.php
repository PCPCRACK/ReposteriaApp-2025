<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = DB::table('Pago as pa')
            ->join('Pedido as pe', 'pa.ped_id', '=', 'pe.ped_id')
            ->leftJoin('Cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
            ->select(
                'pa.pag_id',
                'pa.pag_fec',
                'pa.pag_hora',
                'pa.pag_metodo',
                'pa.ped_id',
                'pe.ped_total',
                'c.cli_nom',
                'c.cli_apellido'
            )
            ->orderByDesc('pa.pag_fec')
            ->orderByDesc('pa.pag_hora')
            ->get();

        $pedidosSinPago = DB::table('Pedido as pe')
            ->leftJoin('Pago as pa', 'pe.ped_id', '=', 'pa.ped_id')
            ->leftJoin('Cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
            ->whereNull('pa.pag_id')
            ->whereNotIn('pe.ped_est', ['Anulado'])
            ->select('pe.ped_id', 'pe.ped_total', 'pe.ped_est', 'pe.ped_fec', 'c.cli_nom', 'c.cli_apellido')
            ->orderByDesc('pe.ped_fec')
            ->get();

        return view('cajero.pagos.index', compact('pagos', 'pedidosSinPago'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ped_id' => 'required|exists:Pedido,ped_id',
            'pag_metodo' => 'required|in:Efectivo,Tarjeta,Transferencia',
        ]);

        $now = Carbon::now();

        DB::table('Pago')->insert([
            'ped_id' => $validated['ped_id'],
            'pag_metodo' => $validated['pag_metodo'],
            'pag_fec' => $now->toDateString(),
            'pag_hora' => $now->toTimeString(),
        ]);

        return redirect()->route('cajero.pagos.index')->with('success', 'Pago registrado correctamente.');
    }
}
