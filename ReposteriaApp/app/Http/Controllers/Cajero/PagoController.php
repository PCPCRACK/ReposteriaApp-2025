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
        $pagos = DB::table('vw_pagos_pedidos_clientes as pc')
            ->select(
                'pc.pag_id',
                'pc.pag_fec',
                'pc.pag_hora',
                'pc.pag_metodo',
                'pc.ped_id',
                'pc.ped_total',
                'pc.cli_nom',
                'pc.cli_apellido'
            )
            ->orderByDesc('pc.pag_fec')
            ->orderByDesc('pc.pag_hora')
            ->get();

        $pedidosSinPago = DB::table('vw_pedido as pe')
            ->leftJoin('vw_pago as pa', 'pe.ped_id', '=', 'pa.ped_id')
            ->leftJoin('vw_cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
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

        DB::statement('CALL sp_registrar_pago(?, ?, ?, ?, @new_pag_id)', [
            $validated['ped_id'],
            $validated['pag_metodo'],
            $now->toDateString(),
            $now->toTimeString(),
        ]);

        return redirect()->route('cajero.pagos.index')->with('success', 'Pago registrado correctamente.');
    }
}
