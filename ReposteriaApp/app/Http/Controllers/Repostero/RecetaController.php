<?php

namespace App\Http\Controllers\Repostero;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index()
    {
        $recetas = DB::table('Receta')->orderBy('rec_nom')->get();

        $detalles = DB::table('DetalleReceta as dr')
            ->join('Ingrediente as i', 'dr.ing_id', '=', 'i.ing_id')
            ->select('dr.rec_id', 'i.ing_nom', 'dr.dre_can', 'i.ing_um')
            ->orderBy('i.ing_nom')
            ->get()
            ->groupBy('rec_id');

        $presentaciones = DB::table('ProductoPresentacion as pp')
            ->join('Producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->join('Receta as r', 'p.rec_id', '=', 'r.rec_id')
            ->select('r.rec_id', 'p.pro_nom', 't.tam_nom', 't.tam_factor')
            ->orderBy('p.pro_nom')
            ->get()
            ->groupBy('rec_id');

        return view('repostero.recetas.index', compact('recetas', 'detalles', 'presentaciones'));
    }
}
