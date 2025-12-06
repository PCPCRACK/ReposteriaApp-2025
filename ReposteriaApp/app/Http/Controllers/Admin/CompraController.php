<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

    public function create()
    {
        $proveedores = DB::table('Proveedor')->orderBy('prov_nom')->get();
        $ingredientes = DB::table('Ingrediente')->orderBy('ing_nom')->get();
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

            $comId = DB::table('Compra')->insertGetId([
                'prov_id' => $request->prov_id,
                'com_fec' => $request->com_fec,
                'com_tot' => $total,
            ]);

            foreach ($request->items as $item) {
                DB::table('DetalleCompra')->insert([
                    'com_id' => $comId,
                    'ing_id' => $item['ing_id'],
                    'dco_can' => $item['dco_can'],
                    'dco_pre' => $item['dco_pre'],
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
        $compra = DB::table('Compra')->where('com_id', $id)->first();
        abort_unless($compra, 404);

        $detalles = DB::table('DetalleCompra as dc')
            ->join('Ingrediente as i', 'dc.ing_id', '=', 'i.ing_id')
            ->where('dc.com_id', $id)
            ->select('dc.ing_id', 'dc.dco_can', 'dc.dco_pre', 'i.ing_nom')
            ->get();

        $proveedores = DB::table('Proveedor')->orderBy('prov_nom')->get();
        $ingredientes = DB::table('Ingrediente')->orderBy('ing_nom')->get();

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

            DB::table('Compra')
                ->where('com_id', $id)
                ->update([
                    'prov_id' => $request->prov_id,
                    'com_fec' => $request->com_fec,
                    'com_tot' => $total,
                ]);

            DB::table('DetalleCompra')->where('com_id', $id)->delete();

            foreach ($request->items as $item) {
                DB::table('DetalleCompra')->insert([
                    'com_id' => $id,
                    'ing_id' => $item['ing_id'],
                    'dco_can' => $item['dco_can'],
                    'dco_pre' => $item['dco_pre'],
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
        $detalles = DB::table('DetalleCompra')->where('com_id', $comId)->get();
        foreach ($detalles as $detalle) {
            DB::table('Ingrediente')
                ->where('ing_id', $detalle->ing_id)
                ->update(['ing_stock' => DB::raw('ing_stock + ' . $detalle->dco_can)]);
        }
    }
}
