<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Repostería</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Dashboard Administrativo</div>
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
                <div class="stats-grid stats-grid-4-cols">
                    <div class="stat-card">
                        <div class="stat-info">
                            <div class="stat-label">Ventas Hoy</div>
                            <div class="stat-value">${{ number_format($ventasHoy, 0, ',', '.') }}</div>
                            <div class="stat-change positive">Ventas entregadas del día</div>
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
                            <div class="stat-change neutral">Pendientes de entrega</div>
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
                            <div class="stat-change warning">{{ $productosBajoStock }} bajo stock</div>
                        </div>
                        <div class="stat-icon products">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 4H10V0H6V4H2L0 6V14C0 14.5304 0.210714 15.0391 0.585786 15.4142C0.960859 15.7893 1.46957 16 2 16H14C14.5304 16 15.0391 15.7893 15.4142 15.4142C15.7893 15.0391 16 14.5304 16 14V6L14 4ZM8 2H8.8L9.2 4H8V2ZM2 6H14V14H2V6Z" fill="#EA580C"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <div class="stat-label">Empleados</div>
                            <div class="stat-value">{{ $empleados }}</div>
                            <div class="stat-change info">{{ $cajeros }} cajeros, {{ $reposteros }} reposteros</div>
                        </div>
                        <div class="stat-icon employees">
                            <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 4C16 2.89 16.89 2 18 2C19.11 2 20 2.89 20 4C20 5.11 19.11 6 18 6C16.89 6 16 5.11 16 4ZM10 6C10 4.89 10.89 4 12 4C13.11 4 14 4.89 14 6C14 7.11 13.11 8 12 8C10.89 8 10 7.11 10 6ZM6 6C6 4.89 6.89 4 8 4C9.11 4 10 4.89 10 6C10 7.11 9.11 8 8 8C6.89 8 6 7.11 6 6ZM0 4C0 2.89 0.89 2 2 2C3.11 2 4 2.89 4 4C4 5.11 3.11 6 2 6C0.89 6 0 5.11 0 4Z" fill="#9333EA"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">Ventas por Mes</div>
                        </div>
                        <div class="chart-body">
                            @if ($ventasPorMes->isEmpty())
                                <div class="empty-state">No hay ventas registradas.</div>
                            @else
                                <div class="chart-container">
                                    <canvas id="ventasBarChart"></canvas>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">Productos Más Vendidos</div>
                        </div>
                        <div class="chart-body">
                            @if ($productosMasVendidos->isEmpty())
                                <div class="empty-state">No hay productos vendidos aún.</div>
                            @else
                                <div class="chart-container">
                                    <canvas id="productosPieChart"></canvas>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Inventory and Orders -->
                <div class="grid-2-cols">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Estado del Inventario</div>
                            <div class="card-subtitle">Revisión de niveles de ingredientes</div>
                        </div>
                        <div class="table-container">
                            <table class="inventory-table">
                                <thead>
                                    <tr>
                                        <th>Ingrediente</th>
                                        <th>Stock actual</th>
                                        <th>Stock mínimo</th>
                                        <th>Proveedor reciente</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($estadoInventario as $item)
                                        <tr>
                                            <td>{{ $item->ing_nom }}</td>
                                            <td>{{ $item->ing_stock }} {{ $item->ing_um }}</td>
                                            <td>{{ $item->ing_reord }} {{ $item->ing_um }}</td>
                                            <td>{{ $item->prov_nom }}</td>
                                            <td>
                                                @if ($item->ing_stock <= $item->ing_reord)
                                                    <span class="status-badge low">Bajo Stock</span>
                                                @else
                                                    <span class="status-badge sufficient">Suficiente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-state">No hay ingredientes registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Pedidos Recientes</div>
                            <div class="card-subtitle">Últimas ventas registradas</div>
                        </div>
                        <div class="table-container">
                            <table class="inventory-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
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
                                                        'Preparado' => 'info',
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
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ventasData = @json($ventasPorMes);
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

    function buildBarChart(canvasId, labels, values) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const colors = labels.map((_, idx) => palette[idx % palette.length]);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                return `${context.label}: ${value.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    if (ventasData.length) {
        const ventasLabels = ventasData.map(item => item.mes);
        const ventasValues = ventasData.map(item => Number(item.total));
        buildBarChart('ventasBarChart', ventasLabels, ventasValues);
    }

    if (productosData.length) {
        const topProductos = productosData.slice(0, 4);
        const productoLabels = topProductos.map(item => `${item.pro_nom} (${item.tam_nom})`);
        const productoValues = topProductos.map(item => Number(item.cantidad));
        buildPieChart('productosPieChart', productoLabels, productoValues);
    }
</script>
</html>
