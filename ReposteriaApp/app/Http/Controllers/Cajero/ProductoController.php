<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = DB::table('Producto as p')
            ->leftJoin('ProductoPresentacion as pp', 'p.pro_id', '=', 'pp.pro_id')
            ->leftJoin('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->select('p.pro_id', 'p.pro_nom', 'pp.prp_id', 'pp.prp_precio', 't.tam_nom')
            ->orderBy('p.pro_nom')
            ->orderBy('t.tam_nom')
            ->get()
            ->groupBy('pro_id');

        return view('cajero.productos.index', compact('productos'));
    }
}
