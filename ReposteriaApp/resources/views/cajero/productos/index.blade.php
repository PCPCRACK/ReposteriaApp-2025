<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Cajero</title>
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        @include('cajero.partials.sidebar')
        <div class="main-content">
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Productos</div>
                    <div class="header-subtitle">Listado de presentaciones y precios</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Catálogo</div>
                    <div class="card-subtitle">Datos provenientes de la base de datos</div>
                </div>
                <div class="table-container">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Tamaño</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($presentaciones as $item)
                                <tr>
                                    <td>{{ $item->pro_nom }}</td>
                                    <td>{{ $item->tam_nom }}</td>
                                    <td>${{ number_format($item->prp_precio, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty-state">No hay productos disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
