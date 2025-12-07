<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.pagos.index', compact('pagos'));
    }
}
