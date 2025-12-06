<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras - Repostería</title>
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
                    <div class="header-title">Compras</div>
                    <div class="header-subtitle">Historial de compras a proveedores</div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.compras.create') }}" class="primary-action-button">Nueva compra</a>
                </div>
            </div>

            <div class="table-container">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>ID Compra</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Total</th>
                            <th>Detalle</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($compras as $compra)
                            <tr>
                                <td>{{ $compra->com_id }}</td>
                                <td>{{ $compra->com_fec }}</td>
                                <td>{{ $compra->prov_nom }}</td>
                                <td>${{ number_format($compra->com_tot, 0, ',', '.') }}</td>
                                <td>{{ $compra->detalle ?? 'Sin detalle de ingredientes' }}</td>
                                <td><a class="filter-button" href="{{ route('admin.compras.edit', $compra->com_id) }}">Editar</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">No hay compras registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
