<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function index()
    {
        $compras = DB::table('vw_compras_detalle_ext as cde')
            ->join('vw_proveedor as p', 'cde.prov_id', '=', 'p.prov_id')
            ->select(
                'cde.com_id',
                'cde.com_fec',
                'cde.com_tot',
                'p.prov_nom',
                DB::raw("GROUP_CONCAT(CONCAT(cde.ing_nom, ' x', cde.dco_can, ' ', cde.ing_um) SEPARATOR ', ') as detalle")
            )
            ->groupBy('cde.com_id', 'cde.com_fec', 'cde.com_tot', 'p.prov_nom')
            ->orderByDesc('cde.com_fec')
            ->get();

        return view('admin.compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = DB::table('vw_proveedor')->orderBy('prov_nom')->get();
        $ingredientes = DB::table('vw_ingrediente')->orderBy('ing_nom')->get();
        return view('admin.compras.create', compact('proveedores', 'ingredientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'prov_id' => 'required|exists:Proveedor,prov_id',
            'com_fec' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.ing_id' => 'required|exists:Ingrediente,ing_id',
            'items.*.dco_can' => 'required|numeric|min:0.01',
            'items.*.dco_pre' => 'required|numeric|min:0',
            'confirmar' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {
            $total = collect($request->items)->reduce(function ($carry, $item) {
                return $carry + ($item['dco_can'] * $item['dco_pre']);
            }, 0);

            DB::statement('CALL sp_crear_compra(?, ?, ?, @new_com_id)', [
                $request->prov_id,
                $request->com_fec,
                $total,
            ]);

            $comId = DB::select('SELECT @new_com_id as com_id')[0]->com_id ?? null;

            foreach ($request->items as $item) {
                DB::statement('CALL sp_agregar_detalle_compra(?, ?, ?, ?)', [
                    $comId,
                    $item['ing_id'],
                    $item['dco_can'],
                    $item['dco_pre'],
                ]);
            }

            if ($request->boolean('confirmar')) {
                $this->actualizarStockCompra($comId);
            }
        });

        return redirect()->route('admin.compras.index')->with('success', 'Compra registrada correctamente.');
    }

    public function edit($id)
    {
        $compra = DB::table('vw_compra')->where('com_id', $id)->first();
        abort_unless($compra, 404);

        $detalles = DB::table('vw_detalle_compra as dc')
            ->join('vw_ingrediente as i', 'dc.ing_id', '=', 'i.ing_id')
            ->where('dc.com_id', $id)
            ->select('dc.ing_id', 'dc.dco_can', 'dc.dco_pre', 'i.ing_nom')
            ->get();

        $proveedores = DB::table('vw_proveedor')->orderBy('prov_nom')->get();
        $ingredientes = DB::table('vw_ingrediente')->orderBy('ing_nom')->get();

        return view('admin.compras.edit', compact('compra', 'detalles', 'proveedores', 'ingredientes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'prov_id' => 'required|exists:Proveedor,prov_id',
            'com_fec' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.ing_id' => 'required|exists:Ingrediente,ing_id',
            'items.*.dco_can' => 'required|numeric|min:0.01',
            'items.*.dco_pre' => 'required|numeric|min:0',
            'confirmar' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $id) {
            $total = collect($request->items)->reduce(function ($carry, $item) {
                return $carry + ($item['dco_can'] * $item['dco_pre']);
            }, 0);

            DB::statement('CALL sp_actualizar_compra(?, ?, ?, ?)', [
                $id,
                $request->prov_id,
                $request->com_fec,
                $total,
            ]);

            DB::statement('CALL sp_eliminar_detalles_compra(?)', [$id]);

            foreach ($request->items as $item) {
                DB::statement('CALL sp_agregar_detalle_compra(?, ?, ?, ?)', [
                    $id,
                    $item['ing_id'],
                    $item['dco_can'],
                    $item['dco_pre'],
                ]);
            }

            if ($request->boolean('confirmar')) {
                $this->actualizarStockCompra($id);
            }
        });

        return redirect()->route('admin.compras.index')->with('success', 'Compra actualizada correctamente.');
    }

    private function actualizarStockCompra(int $comId): void
    {
        DB::statement('CALL sp_aplicar_stock_compra(?)', [$comId]);
    }
}
