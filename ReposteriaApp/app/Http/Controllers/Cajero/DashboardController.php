<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $estadoFiltro = $request->query('estado');
        $fechaFiltro = $request->query('fecha');

        $dineroEnCaja = DB::table('Pago as pa')
            ->join('Pedido as pe', 'pa.ped_id', '=', 'pe.ped_id')
            ->whereDate('pa.pag_fec', $today)
            ->sum('pe.ped_total');

        $pedidosActivos = DB::table('Pedido')
            ->whereIn('ped_est', ['Pendiente', 'Preparado'])
            ->count();

        $pedidosPendientes = DB::table('Pedido')
            ->where('ped_est', 'Pendiente')
            ->count();

        $totalProductos = DB::table('Producto')->count();

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

        $estadoPedidos = DB::table('Pedido')
            ->select(
                'ped_est',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('MAX(ped_fec) as fecha')
            )
            ->groupBy('ped_est')
            ->get();

        $pedidosRecientesQuery = DB::table('Pedido as pe')
            ->leftJoin('Cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
            ->select('pe.ped_id', 'pe.ped_total', 'pe.ped_est', 'pe.ped_fec', 'c.cli_nom', 'c.cli_apellido')
            ->orderByDesc('pe.ped_fec')
            ->orderByDesc('pe.ped_id')
            ->limit(5);

        if ($estadoFiltro && $estadoFiltro !== 'todos') {
            $pedidosRecientesQuery->where('pe.ped_est', $estadoFiltro);
        }

        if ($fechaFiltro) {
            $pedidosRecientesQuery->whereDate('pe.ped_fec', $fechaFiltro);
        }

        $pedidosRecientes = $pedidosRecientesQuery->get();

        $detallesRecientes = DB::table('DetallePedido as dp')
            ->join('ProductoPresentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
            ->join('Producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->whereIn('dp.ped_id', $pedidosRecientes->pluck('ped_id'))
            ->select('dp.ped_id', 'p.pro_nom', 't.tam_nom', 'dp.dpe_can')
            ->get()
            ->groupBy('ped_id');

        $resumenPedidos = $this->construirResumenPedidos($detallesRecientes);

        return view('cajero.dashboardCajero', [
            'dineroEnCaja' => $dineroEnCaja,
            'pedidosActivos' => $pedidosActivos,
            'pedidosPendientes' => $pedidosPendientes,
            'totalProductos' => $totalProductos,
            'productosMasVendidos' => $productosMasVendidos,
            'estadoPedidos' => $estadoPedidos,
            'pedidosRecientes' => $pedidosRecientes,
            'resumenPedidos' => $resumenPedidos,
            'estadoFiltro' => $estadoFiltro,
            'fechaFiltro' => $fechaFiltro,
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
