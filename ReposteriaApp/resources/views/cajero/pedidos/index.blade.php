<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Cajero</title>
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        @include('cajero.partials.sidebar')
        <div class="main-content">
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Pedidos</div>
                    <div class="header-subtitle">Filtra por estado o fecha</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="align-items:center; justify-content: space-between;">
                    <div class="card-title">Listado de pedidos</div>
                    <div class="card-subtitle">Últimos registros</div>
                    <div class="header-actions">
                        <a class="primary-action-button" href="{{ route('cajero.pedidos.create') }}">Nuevo pedido</a>
                    </div>
                </div>
                <div class="table-container compact">
                    <form class="filters-row" style="padding:10px 0; gap:8px;" method="GET" action="{{ route('cajero.pedidos.index') }}">
                        <div class="inventory-search">
                            <select name="estado" class="form-input">
                                <option value="todos">Todos los estados</option>
                                @foreach (['Pendiente','Preparado','Entregado','Anulado'] as $estadoOpt)
                                    <option value="{{ $estadoOpt }}" @if(($estado ?? 'todos') === $estadoOpt) selected @endif>{{ $estadoOpt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="date" name="fecha" class="form-input" value="{{ $fecha }}">
                        <button type="submit" class="filter-button">Filtrar</button>
                        <a href="{{ route('cajero.pedidos.index') }}" class="filter-button secondary">Limpiar</a>
                    </form>
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pedidos as $pedido)
                                <tr>
                                    <td>#{{ $pedido->ped_id }}</td>
                                    <td>{{ trim(($pedido->cli_nom ?? '') . ' ' . ($pedido->cli_apellido ?? '')) ?: 'Cliente ocasional' }}</td>
                                    <td>{{ $resumenPedidos[$pedido->ped_id] ?? 'Sin detalles' }}</td>
                                    <td>${{ number_format($pedido->ped_total, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'Pendiente' => 'pending',
                                                    'Preparado' => 'preparing',
                                                    'Entregado' => 'completed',
                                                    'Anulado' => 'cancelled',
                                                ][$pedido->ped_est] ?? 'pending';
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">{{ $pedido->ped_est }}</span>
                                        </td>
                                        <td>
                                            @if ($pedido->ped_est !== 'Entregado' && $pedido->ped_est !== 'Anulado')
                                                <form method="POST" action="{{ route('cajero.pedidos.estado', $pedido->ped_id) }}">
                                                    @csrf
                                                    <input type="hidden" name="ped_est" value="Entregado">
                                                    <button class="filter-button" type="submit">Marcar Entregado</button>
                                                </form>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $pedido->ped_fec }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">No hay pedidos con los filtros actuales.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $pedidos->links() }}
            </div>
        </div>
    </div>
</body>
</html>
