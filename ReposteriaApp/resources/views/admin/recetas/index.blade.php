<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recetas - Administrador</title>
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        @include('admin.partials.sidebar')

        <div class="main-content">
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Recetas</div>
                    <div class="header-subtitle">Ingredientes base y presentaciones</div>
                </div>
                <div class="header-actions">
                    <button class="primary-action-button" disabled title="Agregar receta (pendiente de implementación)">Agregar receta</button>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="justify-content: space-between; align-items: center;">
                    <div>
                        <div class="card-title">Listado de recetas</div>
                        <div class="card-subtitle">Vista alineada a Repostero</div>
                    </div>
                    <div class="filters-row" style="gap: 8px;">
                        <input type="search" id="receta-filter" class="form-input" placeholder="Buscar por nombre...">
                    </div>
                </div>
                <div class="table-container compact">
                    <table class="inventory-table" id="recetas-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Ingredientes</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recetas as $receta)
                                <tr>
                                    <td>{{ $receta->rec_id }}</td>
                                    <td>{{ $receta->rec_nom }}</td>
                                    <td>{{ $receta->ingredientes }}</td>
                                    <td>{{ $receta->detalle ?? 'Sin ingredientes registrados' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty-state">No hay recetas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function(){
            const input = document.getElementById('receta-filter');
            const rows = Array.from(document.querySelectorAll('#recetas-table tbody tr'));
            if (!input) return;
            input.addEventListener('input', () => {
                const q = input.value.toLowerCase();
                rows.forEach(r => {
                    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        })();
    </script>
</body>
</html>
