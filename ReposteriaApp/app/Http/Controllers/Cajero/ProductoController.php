<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = DB::table('vw_productos_presentaciones_ext')
            ->select('pro_id', 'pro_nom', 'prp_id', 'prp_precio', 'tam_nom')
            ->orderBy('pro_nom')
            ->orderBy('tam_nom')
            ->get()
            ->groupBy('pro_id');

        return view('cajero.productos.index', compact('productos'));
    }
}
