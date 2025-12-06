<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Cajero;
use App\Models\Pedido;
use App\Models\DetallePedido;

class PedidoController extends Controller
{
    public function create()
    {
        $clientes = Cliente::all();
        $productos = Producto::with('presentaciones.tamano')->get();
        $cajeros = Cajero::with('empleado')->get();
        return view('cajero.pedidos.create', compact('clientes', 'productos', 'cajeros'));
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

            $pedido = Pedido::create([
                'cli_cedula' => $request->cli_cedula,
                'emp_id' => $request->emp_id,
                'ped_fec' => now()->toDateString(),
                'ped_hora' => now()->toTimeString(),
                'ped_est' => 'Pendiente',
                'ped_total' => $total,
            ]);

            foreach ($request->items as $item) {
                DetallePedido::create([
                    'ped_id' => $pedido->ped_id,
                    'prp_id' => $item['prp_id'],
                    'dpe_can' => $item['quantity'],
                    'dpe_subtotal' => $item['quantity'] * $item['price'],
                ]);
            }
        });

        return redirect()->route('cajero.pedidos.index')->with('success', 'Pedido creado correctamente.');
    }

    public function index(Request $request)
    {
        $estado = $request->query('estado');
        $fecha = $request->query('fecha');

        $query = DB::table('Pedido as pe')
            ->leftJoin('Cliente as c', 'pe.cli_cedula', '=', 'c.cli_cedula')
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

        $detalles = DB::table('DetallePedido as dp')
            ->join('ProductoPresentacion as pp', 'dp.prp_id', '=', 'pp.prp_id')
            ->join('Producto as p', 'pp.pro_id', '=', 'p.pro_id')
            ->join('Tamano as t', 'pp.tam_id', '=', 't.tam_id')
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

        DB::table('Pedido')->where('ped_id', $pedidoId)->update([
            'ped_est' => $request->ped_est,
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
