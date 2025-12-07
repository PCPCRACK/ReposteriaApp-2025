<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function create()
    {
        $clientes = DB::table('vw_cliente')->get();

        $presentaciones = DB::table('vw_productos_presentaciones_ext')
            ->orderBy('pro_nom')
            ->orderBy('tam_nom')
            ->get()
            ->groupBy('pro_id')
            ->map(function ($items) {
                return (object) [
                    'pro_id' => $items->first()->pro_id,
                    'pro_nom' => $items->first()->pro_nom,
                    'presentaciones' => $items->map(function ($item) {
                        return (object) [
                            'prp_id' => $item->prp_id,
                            'prp_precio' => $item->prp_precio,
                            'tamano' => (object) [
                                'tam_nom' => $item->tam_nom,
                                'tam_factor' => $item->tam_factor,
                            ],
                        ];
                    })->values()->all(),
                ];
            })
            ->values();

        $cajeros = DB::table('vw_cajero as c')
            ->join('vw_empleado as e', 'c.emp_id', '=', 'e.emp_id')
            ->select('c.emp_id', 'e.emp_nom')
            ->orderBy('e.emp_nom')
            ->get();

        return view('cajero.pedidos.create', [
            'clientes' => $clientes,
            'productos' => $presentaciones,
            'cajeros' => $cajeros,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cli_cedula' => 'required|exists:cliente,cli_cedula',
            'emp_id' => 'required|exists:cajero,emp_id',
            'items' => 'required|array|min:1',
            'items.*.prp_id' => 'required|exists:productopresentacion,prp_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $total = collect($request->items)->reduce(function ($carry, $item) {
                return $carry + ($item['quantity'] * $item['price']);
            }, 0);

            DB::statement('CALL sp_crear_pedido(?, ?, ?, ?, ?, ?, @new_ped_id)', [
                $request->cli_cedula,
                $request->emp_id,
                'Pendiente',
                $total,
                now()->toDateString(),
                now()->toTimeString(),
            ]);

            $pedidoId = DB::select('SELECT @new_ped_id as ped_id')[0]->ped_id ?? null;

            foreach ($request->items as $item) {
                DB::statement('CALL sp_agregar_detalle_pedido(?, ?, ?, ?)', [
                    $pedidoId,
                    $item['prp_id'],
                    $item['quantity'],
                    $item['quantity'] * $item['price'],
                ]);
            }
        });

        return redirect()->route('cajero.pedidos.index')->with('success', 'Pedido creado correctamente.');
    }

    public function index(Request $request)
    {
        $estado = $request->query('estado');
        $fecha = $request->query('fecha');

        $query = DB::table('vw_pedido as pe')
            ->leftJoin('vw_cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
            ->select('pe.ped_id', 'pe.ped_total', 'pe.ped_est', 'pe.ped_fec', 'c.cli_nom', 'c.cli_apellido')
            ->orderByDesc('pe.ped_fec')
            ->orderByDesc('pe.ped_id');

        if ($estado && $estado !== 'todos') {
            $query->where('pe.ped_est', $estado);
        }

        if ($fecha) {
            $query->whereDate('pe.ped_fec', $fecha);
        }

        $pedidos = $query->paginate(10)->withQueryString();

        $detalles = DB::table('vw_detalle_pedido as dp')
            ->join('vw_producto_presentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
            ->join('vw_producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('vw_tamano as t', 'pp.tam_id', '=', 't.tam_id')
            ->whereIn('dp.ped_id', $pedidos->pluck('ped_id'))
            ->select('dp.ped_id', 'p.pro_nom', 't.tam_nom', 'dp.dpe_can')
            ->get()
            ->groupBy('ped_id');

        $resumenPedidos = $this->construirResumenPedidos($detalles);

        return view('cajero.pedidos.index', [
            'pedidos' => $pedidos,
            'resumenPedidos' => $resumenPedidos,
            'estado' => $estado,
            'fecha' => $fecha,
        ]);
    }

    public function updateEstado(Request $request, $pedidoId)
    {
        $request->validate([
            'ped_est' => 'required|in:Pendiente,Preparado,Entregado,Anulado',
        ]);

        DB::statement('CALL sp_actualizar_estado_pedido(?, ?)', [
            $pedidoId,
            $request->ped_est,
        ]);

        return back()->with('success', 'Estado actualizado.');
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
