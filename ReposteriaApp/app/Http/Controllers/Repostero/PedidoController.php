<?php

namespace App\Http\Controllers\Repostero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function preparar($pedidoId)
    {
        DB::transaction(function () use ($pedidoId) {
            $pedido = DB::table('Pedido')->where('ped_id', $pedidoId)->first();
            if (!$pedido || $pedido->ped_est !== 'Pendiente') {
                return;
            }

            $detalles = DB::table('DetallePedido as dp')
                ->join('ProductoPresentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
                ->join('Producto as p', 'pp.pro_id', '=', 'p.pro_id')
                ->join('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
                ->join('Receta as r', 'p.rec_id', '=', 'r.rec_id')
                ->join('DetalleReceta as dr', 'r.rec_id', '=', 'dr.rec_id')
                ->select('dr.ing_id', 'dr.dre_can', 't.tam_factor', 'dp.dpe_can')
                ->where('dp.ped_id', $pedidoId)
                ->get();

            foreach ($detalles as $detalle) {
                $consumo = $detalle->dre_can * $detalle->tam_factor * $detalle->dpe_can;
                DB::table('Ingrediente')
                    ->where('ing_id', $detalle->ing_id)
                    ->update(['ing_stock' => DB::raw('ing_stock - ' . $consumo)]);
            }

            DB::table('Pedido')
                ->where('ped_id', $pedidoId)
                ->update(['ped_est' => 'Preparado']);
        });

        return redirect()->route('repostero.dashboard')->with('success', 'Pedido actualizado a Preparado.');
    }
}
