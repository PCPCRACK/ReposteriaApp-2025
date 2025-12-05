<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $compras = DB::table('Compra as c')
            ->join('Proveedor as p', 'c.prov_id', '=', 'p.prov_id')
            ->leftJoin('DetalleCompra as dc', 'c.com_id', '=', 'dc.com_id')
            ->leftJoin('Ingrediente as i', 'dc.ing_id', '=', 'i.ing_id')
            ->select(
                'c.com_id',
                'c.com_fec',
                'c.com_tot',
                'p.prov_nom',
                DB::raw("GROUP_CONCAT(CONCAT(i.ing_nom, ' x', dc.dco_can, ' ', i.ing_um) SEPARATOR ', ') as detalle")
            )
            ->groupBy('c.com_id', 'c.com_fec', 'c.com_tot', 'p.prov_nom')
            ->orderByDesc('c.com_fec')
            ->get();

        return view('admin.compras.index', compact('compras'));
    }
}
