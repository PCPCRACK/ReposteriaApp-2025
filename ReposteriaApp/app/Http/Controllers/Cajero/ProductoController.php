<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $presentaciones = DB::table('ProductoPresentacion as pp')
            ->join('Producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->select('p.pro_nom', 't.tam_nom', 'pp.prp_precio')
            ->orderBy('p.pro_nom')
            ->orderBy('t.tam_nom')
            ->get();

        return view('cajero.productos.index', compact('presentaciones'));
    }
}
