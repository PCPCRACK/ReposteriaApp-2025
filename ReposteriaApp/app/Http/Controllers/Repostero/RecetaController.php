<?php

namespace App\Http\Controllers\Repostero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index()
    {
        $recetas = DB::table('vw_receta')->orderBy('rec_nom')->get();

        $detalles = DB::table('vw_detalle_receta as dr')
            ->join('vw_ingrediente as i', 'dr.ing_id', '=', 'i.ing_id')
            ->select('dr.rec_id', 'i.ing_nom', 'dr.dre_can', 'i.ing_um')
            ->orderBy('i.ing_nom')
            ->get()
            ->groupBy('rec_id');

        $presentaciones = DB::table('vw_producto_presentacion as pp')
            ->join('vw_producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('vw_tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->join('vw_receta as r', 'p.rec_id', '=', 'r.rec_id')
            ->select('r.rec_id', 'p.pro_nom', 't.tam_nom', 't.tam_factor')
            ->orderBy('p.pro_nom')
            ->get()
            ->groupBy('rec_id');

        return view('repostero.recetas.index', compact('recetas', 'detalles', 'presentaciones'));
    }
}
