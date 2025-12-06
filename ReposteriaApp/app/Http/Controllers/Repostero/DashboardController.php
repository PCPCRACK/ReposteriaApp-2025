<?php

namespace App\Http\Controllers\Repostero;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $invFilter = request('inv_filter');

        $pedidosActivos = DB::table('Pedido')
            ->whereIn('ped_est', ['Pendiente', 'Preparado'])
            ->count();

        $pedidosPendientes = DB::table('Pedido')
            ->where('ped_est', 'Pendiente')
            ->count();

        $totalProductos = DB::table('Producto')->count();

        $totalIngredientes = DB::table('Ingrediente')->count();

        $ingredientesBajoStock = DB::table('Ingrediente')
            ->whereColumn('ing_stock', '<=', 'ing_reord')
            ->count();

        $stockChart = DB::table('Ingrediente')
            ->select('ing_nom', 'ing_stock', 'ing_reord')
            ->orderBy('ing_nom')
            ->get();

        $productosMasVendidos = DB::table('DetallePedido as dp')
            ->join('ProductoPresentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
            ->join('Producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->select(
                'p.pro_nom',
                't.tam_nom',
                DB::raw('SUM(dp.dpe_can) as cantidad')
            )
            ->groupBy('p.pro_nom', 't.tam_nom')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();

        $latestPurchases = DB::table('DetalleCompra as dc')
            ->join('Compra as c', 'dc.com_id', '=', 'c.com_id')
            ->select('dc.ing_id', DB::raw('MAX(c.com_fec) as last_date'))
            ->groupBy('dc.ing_id');

        $estadoInventarioQuery = DB::table('Ingrediente as i')
            ->leftJoinSub($latestPurchases, 'lp', function ($join) {
                $join->on('i.ing_id', '=', 'lp.ing_id');
            })
            ->leftJoin('DetalleCompra as dc', function ($join) {
                $join->on('i.ing_id', '=', 'dc.ing_id');
            })
            ->leftJoin('Compra as c', function ($join) {
                $join->on('dc.com_id', '=', 'c.com_id');
            })
            ->leftJoin('Proveedor as p', 'c.prov_id', '=', 'p.prov_id')
            ->where(function ($query) {
                $query->whereNull('lp.last_date')
                    ->orWhereColumn('c.com_fec', 'lp.last_date');
            })
            ->select(
                'i.ing_id',
                'i.ing_nom',
                'i.ing_stock',
                'i.ing_reord',
                'i.ing_um',
                DB::raw('COALESCE(p.prov_nom, "Sin proveedor") as prov_nom')
            )
            ->groupBy(
                'i.ing_id',
                'i.ing_nom',
                'i.ing_stock',
                'i.ing_reord',
                'i.ing_um',
                DB::raw('COALESCE(p.prov_nom, "Sin proveedor")')
            )
            ->orderBy('i.ing_nom');

        if ($invFilter === 'low') {
            $estadoInventarioQuery->havingRaw('MIN(i.ing_stock <= i.ing_reord)');
        }

        $estadoInventario = $estadoInventarioQuery->get();

        $pedidosRecientes = DB::table('Pedido as pe')
            ->leftJoin('Cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
            ->select('pe.ped_id', 'pe.ped_total', 'pe.ped_est', 'pe.ped_fec', 'c.cli_nom', 'c.cli_apellido')
            ->orderByDesc('pe.ped_fec')
            ->orderByDesc('pe.ped_id')
            ->limit(5)
            ->get();

        $detallesRecientes = DB::table('DetallePedido as dp')
            ->join('ProductoPresentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
            ->join('Producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->whereIn('dp.ped_id', $pedidosRecientes->pluck('ped_id'))
            ->select('dp.ped_id', 'p.pro_nom', 't.tam_nom', 'dp.dpe_can')
            ->get()
            ->groupBy('ped_id');

        $resumenPedidos = $this->construirResumenPedidos($detallesRecientes);

        $pedidosTrabajo = DB::table('Pedido as pe')
            ->leftJoin('Cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
            ->select('pe.ped_id', 'pe.ped_total', 'pe.ped_est', 'pe.ped_fec', 'c.cli_nom', 'c.cli_apellido')
            ->whereIn('pe.ped_est', ['Pendiente', 'Preparado'])
            ->orderByDesc('pe.ped_fec')
            ->orderByDesc('pe.ped_id')
            ->limit(15)
            ->get();

        $detallesTrabajo = DB::table('DetallePedido as dp')
            ->join('ProductoPresentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
            ->join('Producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->whereIn('dp.ped_id', $pedidosTrabajo->pluck('ped_id'))
            ->select('dp.ped_id', 'p.pro_nom', 't.tam_nom', 'dp.dpe_can')
            ->get()
            ->groupBy('ped_id');

        $resumenTrabajo = $this->construirResumenPedidos($detallesTrabajo);

        return view('repostero.dashboardRepostero', [
            'pedidosActivos' => $pedidosActivos,
            'pedidosPendientes' => $pedidosPendientes,
            'totalProductos' => $totalProductos,
            'totalIngredientes' => $totalIngredientes,
            'ingredientesBajoStock' => $ingredientesBajoStock,
            'stockChart' => $stockChart,
            'productosMasVendidos' => $productosMasVendidos,
            'estadoInventario' => $estadoInventario,
            'pedidosRecientes' => $pedidosRecientes,
            'resumenPedidos' => $resumenPedidos,
            'pedidosTrabajo' => $pedidosTrabajo,
            'resumenTrabajo' => $resumenTrabajo,
            'invFilter' => $invFilter,
        ]);
    }

    private function construirResumenPedidos(Collection $detallesRecientes): array
    {
        $resumen = [];

        foreach ($detallesRecientes as $pedId => $items) {
            $resumen[$pedId] = $items->map(function ($detalle) {
                return $detalle->pro_nom . ' (' . $detalle->tam_nom . ') x' . $detalle->dpe_can;
            })->implode(', ');
        }

        return $resumen;
    }
}
