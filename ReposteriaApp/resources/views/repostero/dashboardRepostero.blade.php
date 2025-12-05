<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Repostero</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <div class="logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L13.09 8.26L20 9.27L15 14.14L16.18 21.02L12 17.77L7.82 21.02L9 14.14L4 9.27L10.91 8.26L12 2Z" fill="#1F2937"/>
                    </svg>
                </div>
                <div class="logo-text">
                    <div>reposteria</div>
                    <div class="logo-subtitle">REPOSTERO</div>
                </div>
            </div>
            <div class="nav">
                <div class="nav-item">
                    <a href="{{ route('repostero.dashboard') }}" class="nav-link">
                        <div class="nav-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 13H11V3H3V13ZM3 21H11V15H3V21ZM13 21H21V11H13V21ZM13 3V9H21V3H13Z" fill="#1F2937"/>
                            </svg>
                        </div>
                        <div class="nav-text">Dashboard</div>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#productos-section" class="nav-link">
                        <div class="nav-icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 4H10V0H6V4H2L0 6V14C0 14.5304 0.210714 15.0391 0.585786 15.4142C0.960859 15.7893 1.46957 16 2 16H14C14.5304 16 15.0391 15.7893 15.4142 15.4142C15.7893 15.0391 16 14.5304 16 14V6L14 4ZM8 2H8.8L9.2 4H8V2ZM2 6H14V14H2V6Z" fill="#4B5563"/>
                            </svg>
                        </div>
                        <div class="nav-text">Productos</div>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#stock-section" class="nav-link">
                        <div class="nav-icon">
                            <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 0L0 3V7C0 11.2 3.2 14.7 7 16C10.8 14.7 14 11.2 14 7V3L7 0ZM12 7C12 10.1 9.9 12.9 7 14C4.1 12.9 2 10.1 2 7V4.3L7 2.2L12 4.3V7Z" fill="#4B5563"/>
                            </svg>
                        </div>
                        <div class="nav-text">Recetas</div>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#pedidos-section" class="nav-link">
                        <div class="nav-icon">
                            <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 0H2C0.9 0 0 0.9 0 2V14C0 15.1 0.9 16 2 16H10C11.1 16 12 15.1 12 14V2C12 0.9 11.1 0 10 0ZM10 14H2V2H10V14ZM4 10H6V12H4V10ZM4 7H6V9H4V7ZM4 4H6V6H4V4ZM8 10H10V12H8V10ZM8 7H10V9H8V7ZM8 4H10V6H8V4Z" fill="#4B5563"/>
                            </svg>
                        </div>
                        <div class="nav-text">Pedidos</div>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Dashboard</div>
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
                            <div class="stat-change warning">{{ $ingredientesBajoStock }} bajo stock</div>
                        </div>
                        <div class="stat-icon products">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 4H10V0H6V4H2L0 6V14C0 14.5304 0.210714 15.0391 0.585786 15.4142C0.960859 15.7893 1.46957 16 2 16H14C14.5304 16 15.0391 15.7893 15.4142 15.4142C15.7893 15.0391 16 14.5304 16 14V6L14 4ZM8 2H8.8L9.2 4H8V2ZM2 6H14V14H2V6Z" fill="#EA580C"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <div class="stat-label">Almacen</div>
                            <div class="stat-value">{{ $totalIngredientes }}</div>
                            <div class="stat-change info">{{ $ingredientesBajoStock }} bajo stock</div>
                        </div>
                        <div class="stat-icon employees">
                            <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 0H2C0.9 0 0 0.9 0 2V14C0 15.1 0.9 16 2 16H18C19.1 16 20 15.1 20 14V2C20 0.9 19.1 0 18 0ZM18 14H2V2H18V14ZM4 10H6V12H4V10ZM4 7H6V9H4V7ZM4 4H6V6H4V4ZM8 10H16V12H8V10ZM8 7H16V9H8V7ZM8 4H16V6H8V4Z" fill="#A855F7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Section -->
                <div class="charts-grid">
                    <div class="chart-card" id="stock-section">
                        <div class="chart-header">
                            <div class="chart-title">Stock</div>
                            <div class="chart-filter">
                                <span>Actualizado</span>
                            </div>
                        </div>
                        <div class="chart-container">
                            @if ($stockChart->isEmpty())
                                <div class="empty-state">Sin ingredientes registrados.</div>
                            @else
                                <canvas id="stockBarChart"></canvas>
                            @endif
                        </div>
                    </div>
                    <div class="chart-card" id="productos-section">
                        <div class="chart-header">
                            <div class="chart-title">Productos Más Vendidos</div>
                            <div class="chart-filter">
                                <span>Últimos pedidos</span>
                                <svg width="8" height="5" viewBox="0 0 8 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 5L0 0H8L4 5Z" fill="black"/>
                                </svg>
                            </div>
                        </div>
                        <div class="chart-container">
                            @if ($productosMasVendidos->isEmpty())
                                <div class="empty-state">No hay productos vendidos aún.</div>
                            @else
                                <canvas id="productosPieChart"></canvas>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Inventory Table -->
                <div class="inventory-card" id="inventario">
                    <div class="inventory-header">
                        <div class="inventory-title">Estado del Inventario</div>
                        <div class="inventory-actions">
                            <div class="inventory-search">
                                <input type="text" placeholder="Buscar ingrediente..." class="search-input">
                            </div>
                            <button class="filter-button">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V2H14V0H0ZM0 7H14V5H0V7ZM0 12H14V10H0V12Z" fill="#1F2937"/>
                                </svg>
                                Filtrar
                            </button>
                        </div>
                    </div>
                    <div class="table-container">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Ingrediente</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Proveedor</th>
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
                
                <!-- Recent Orders Section -->
                <div class="orders-card" id="pedidos-section">
                    <div class="orders-header">
                        <div class="orders-title">Pedidos Recientes</div>
                        <div class="orders-actions">
                            <div class="filter-select">
                                <span>Todos los estados</span>
                                <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 7L0 0H12L6 7Z" fill="black"/>
                                </svg>
                            </div>
                            <div class="date-selector">
                                <span>mm/dd/yyyy</span>
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="3" width="14" height="13" rx="1" stroke="black" stroke-width="1.5"/>
                                    <path d="M2 6H16" stroke="black" stroke-width="1.5"/>
                                    <path d="M5 1V3" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M13 1V3" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
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
    const stockData = @json($stockChart);
    const productosData = @json($productosMasVendidos);

    const palette = [
        '#2563EB', '#EA580C', '#16A34A', '#9333EA', '#F59E0B', '#0EA5E9',
        '#EF4444', '#10B981', '#8B5CF6', '#EC4899', '#F97316', '#22D3EE'
    ];

    function buildBarChartWithTarget(canvasId, labels, values, targets) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Stock',
                        data: values,
                        backgroundColor: '#2563EB'
                    },
                    {
                        label: 'Mínimo',
                        data: targets,
                        backgroundColor: '#F59E0B'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
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

    if (stockData.length) {
        const labels = stockData.map(item => item.ing_nom);
        const stockValues = stockData.map(item => Number(item.ing_stock));
        const stockTargets = stockData.map(item => Number(item.ing_reord));
        buildBarChartWithTarget('stockBarChart', labels, stockValues, stockTargets);
    }

    if (productosData.length) {
        const labels = productosData.map(item => `${item.pro_nom} (${item.tam_nom})`);
        const values = productosData.map(item => Number(item.cantidad));
        buildPieChart('productosPieChart', labels, values);
    }
</script>
</body>
</html>
