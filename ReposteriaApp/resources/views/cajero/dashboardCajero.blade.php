<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cajero</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        @include('cajero.partials.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Dashboard Cajero</div>
                    <div class="header-subtitle">Resumen general de la repostería</div>
                </div>
                <div class="header-actions">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-button">Cerrar Sesión</button>
                    </form>
                </div>
            </div>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Cards -->
                <div class="stats-grid stats-grid-3-cols">
                    <div class="stat-card">
                        <div class="stat-info">
                            <div class="stat-label">Dinero en caja</div>
                            <div class="stat-value">${{ number_format($dineroEnCaja, 0, ',', '.') }}</div>
                            <div class="stat-change positive">Pagos del día</div>
                        </div>
                        <div class="stat-icon sales">
                            <svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 0L0 5H2V16H8V5H10L5 0Z" fill="#16A34A"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <div class="stat-label">Pedidos Activos</div>
                            <div class="stat-value">{{ $pedidosActivos }}</div>
                            <div class="stat-change neutral">{{ $pedidosPendientes }} pendientes</div>
                        </div>
                        <div class="stat-icon orders">
                            <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 0H2C0.9 0 0 0.9 0 2V14C0 15.1 0.9 16 2 16H10C11.1 16 12 15.1 12 14V2C12 0.9 11.1 0 10 0ZM10 14H2V2H10V14ZM4 10H6V12H4V10ZM4 7H6V9H4V7ZM4 4H6V6H4V4ZM8 10H10V12H8V10ZM8 7H10V9H8V7ZM8 4H10V6H8V4Z" fill="#2563EB"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <div class="stat-label">Productos</div>
                            <div class="stat-value">{{ $totalProductos }}</div>
                            <div class="stat-change warning">Catálogo disponible</div>
                        </div>
                        <div class="stat-icon products">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 4H10V0H6V4H2L0 6V14C0 14.5304 0.210714 15.0391 0.585786 15.4142C0.960859 15.7893 1.46957 16 2 16H14C14.5304 16 15.0391 15.7893 15.4142 15.4142C15.7893 15.0391 16 14.5304 16 14V6L14 4ZM8 2H8.8L9.2 4H8V2ZM2 6H14V14H2V6Z" fill="#EA580C"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Section -->
                <div class="charts-grid">
                    <div class="chart-card" id="productos-section">
                        <div class="chart-header">
                            <div class="chart-title">Productos Más Vendidos</div>
                            </div>
                        <div class="chart-container">
                            @if ($productosMasVendidos->isEmpty())
                                <div class="empty-state">No hay ventas registradas.</div>
                            @else
                                <canvas id="productosPieChart"></canvas>
                            @endif
                        </div>
                    </div>
                    <div class="chart-card" id="pagos-section">
                        <div class="chart-header">
                            <div class="chart-title">Estado Pedidos</div>
                        </div>
                        <div class="table-container">
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Cantidad</th>
                                        <th>Estado</th>
                                        <th>Fecha más reciente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($estadoPedidos as $estado)
                                        <tr>
                                            <td>{{ $estado->cantidad }}</td>
                                            @php
                                                $statusClass = [
                                                    'Pendiente' => 'pending',
                                                    'Preparado' => 'preparing',
                                                    'Entregado' => 'completed',
                                                    'Anulado' => 'cancelled',
                                                ][$estado->ped_est] ?? 'pending';
                                            @endphp
                                            <td><span class="status-badge {{ $statusClass }}">{{ $estado->ped_est }}</span></td>
                                            <td>{{ $estado->fecha }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="empty-state">No hay pedidos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders Section -->
                <div class="orders-card" id="pedidos-section">
                    <div class="orders-header">
                        <div class="orders-title">Pedidos Recientes</div>
                        <form class="orders-actions" method="GET" action="{{ route('cajero.dashboard') }}">
                            <select name="estado" class="form-input">
                                <option value="todos">Todos los estados</option>
                                @foreach (['Pendiente','Preparado','Entregado','Anulado'] as $estadoOpt)
                                    <option value="{{ $estadoOpt }}" @if(($estadoFiltro ?? 'todos') === $estadoOpt) selected @endif>{{ $estadoOpt }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="fecha" class="form-input" value="{{ $fechaFiltro }}">
                            <button type="submit" class="filter-button">Filtrar</button>
                            <a href="{{ route('cajero.dashboard') }}" class="filter-button secondary">Limpiar</a>
                        </form>
                    </div>
                    <div class="table-container">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>ID Pedido</th>
                                    <th>Cliente</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pedidosRecientes as $pedido)
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
                                        <td>{{ $pedido->ped_fec }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="empty-state">No hay pedidos recientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const productosData = @json($productosMasVendidos);

    const palette = [
        '#2563EB', '#EA580C', '#16A34A', '#9333EA', '#F59E0B', '#0EA5E9',
        '#EF4444', '#10B981', '#8B5CF6', '#EC4899', '#F97316', '#22D3EE'
    ];

    function buildPieChart(canvasId, labels, values) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const colors = labels.map((_, idx) => palette[idx % palette.length]);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                return `${context.label}: ${value.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });
    }

    if (productosData.length) {
        const labels = productosData.map(item => `${item.pro_nom} (${item.tam_nom})`);
        const values = productosData.map(item => Number(item.cantidad));
        buildPieChart('productosPieChart', labels, values);
    }
</script>
</body>
</html>
