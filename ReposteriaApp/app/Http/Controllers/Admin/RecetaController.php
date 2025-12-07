<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index()
    {
        $recetas = DB::table('vw_receta as r')
            ->leftJoin('vw_detalle_receta as dr', 'r.rec_id', '=', 'dr.rec_id')
            ->leftJoin('vw_ingrediente as i', 'dr.ing_id', '=', 'i.ing_id')
            ->select(
                'r.rec_id',
                'r.rec_nom',
                DB::raw('COUNT(dr.ing_id) as ingredientes'),
                DB::raw("GROUP_CONCAT(CONCAT(i.ing_nom, ' (', dr.dre_can, ' ', i.ing_um, ')') SEPARATOR ', ') as detalle")
            )
            ->groupBy('r.rec_id', 'r.rec_nom')
            ->orderBy('r.rec_nom')
            ->get();

        return view('admin.recetas.index', compact('recetas'));
    }
}
