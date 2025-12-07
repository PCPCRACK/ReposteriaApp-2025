<?php

namespace App\Http\Controllers\Repostero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function preparar($pedidoId)
    {
        DB::statement('CALL sp_preparar_pedido(?)', [$pedidoId]);

        return redirect()->route('repostero.dashboard')->with('success', 'Pedido actualizado a Preparado.');
    }
}
