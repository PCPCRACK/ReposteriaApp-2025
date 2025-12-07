<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $ventasHoy = DB::table('vw_pedido')
            ->whereDate('ped_fec', $today)
            ->where('ped_est', 'Entregado')
            ->sum('ped_total');

        $pedidosActivos = DB::table('vw_pedido')
            ->where('ped_est', 'Pendiente')
            ->count();

        $totalProductos = DB::table('vw_producto')->count();

        $productosBajoStock = DB::table('vw_ingrediente')
            ->whereColumn('ing_stock', '<=', 'ing_reord')
            ->count();

        $empleados = DB::table('vw_empleado')->count();
        $cajeros = DB::table('vw_cajero')->count();
        $reposteros = DB::table('vw_repostero')->count();

        $ventasPorMes = DB::table('vw_pedido')
            ->select(
                DB::raw("DATE_FORMAT(ped_fec, '%Y-%m') as mes"),
                DB::raw('SUM(ped_total) as total')
            )
            ->where('ped_est', 'Entregado')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $productosMasVendidos = DB::table('vw_detalle_pedido as dp')
            ->join('vw_producto_presentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
            ->join('vw_producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('vw_tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->select(
                'p.pro_nom',
                't.tam_nom',
                DB::raw('SUM(dp.dpe_can) as cantidad')
            )
            ->groupBy('p.pro_nom', 't.tam_nom')
            ->orderByDesc('cantidad')
            ->limit(10)
            ->get();

        $latestPurchases = DB::table('vw_detalle_compra as dc')
            ->join('vw_compra as c', 'dc.com_id', '=', 'c.com_id')
            ->select('dc.ing_id', DB::raw('MAX(c.com_fec) as last_date'))
            ->groupBy('dc.ing_id');

        $estadoInventario = DB::table('vw_ingrediente as i')
            ->leftJoinSub($latestPurchases, 'lp', function ($join) {
                $join->on('i.ing_id', '=', 'lp.ing_id');
            })
            ->leftJoin('vw_detalle_compra as dc', function ($join) {
                $join->on('i.ing_id', '=', 'dc.ing_id');
            })
            ->leftJoin('vw_compra as c', function ($join) {
                $join->on('dc.com_id', '=', 'c.com_id');
            })
            ->leftJoin('vw_proveedor as p', 'c.prov_id', '=', 'p.prov_id')
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
            ->orderBy('i.ing_nom')
            ->get();

        $pedidosRecientes = DB::table('vw_pedido as pe')
            ->leftJoin('vw_cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
            ->select('pe.ped_id', 'pe.ped_total', 'pe.ped_est', 'pe.ped_fec', 'c.cli_nom', 'c.cli_apellido')
            ->orderByDesc('pe.ped_fec')
            ->orderByDesc('pe.ped_id')
            ->limit(5)
            ->get();

        $detallesRecientes = DB::table('vw_detalle_pedido as dp')
            ->join('vw_producto_presentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
            ->join('vw_producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('vw_tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->whereIn('dp.ped_id', $pedidosRecientes->pluck('ped_id'))
            ->select('dp.ped_id', 'p.pro_nom', 't.tam_nom', 'dp.dpe_can')
            ->get()
            ->groupBy('ped_id');

        $resumenPedidos = $this->construirResumenPedidos($detallesRecientes);

        return view('admin.dashboardAdmin', [
            'ventasHoy' => $ventasHoy,
            'pedidosActivos' => $pedidosActivos,
            'totalProductos' => $totalProductos,
            'productosBajoStock' => $productosBajoStock,
            'empleados' => $empleados,
            'cajeros' => $cajeros,
            'reposteros' => $reposteros,
            'ventasPorMes' => $ventasPorMes,
            'productosMasVendidos' => $productosMasVendidos,
            'estadoInventario' => $estadoInventario,
            'pedidosRecientes' => $pedidosRecientes,
            'resumenPedidos' => $resumenPedidos,
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
