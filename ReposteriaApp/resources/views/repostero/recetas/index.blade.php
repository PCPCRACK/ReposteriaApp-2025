<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recetas - Repostero</title>
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        @include('repostero.partials.sidebar')

        <div class="main-content">
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Recetas</div>
                    <div class="header-subtitle">Pasos de insumos por porción base</div>
                </div>
            </div>

            <div class="card">
                <div class="table-container">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>Receta</th>
                                <th>Ingredientes (cantidad base)</th>
                                <th>Presentaciones (factor)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recetas as $receta)
                                <tr>
                                    <td>{{ $receta->rec_nom }}</td>
                                    <td>
                                        @if (($detalles[$receta->rec_id] ?? collect())->isEmpty())
                                            <span class="empty-state">Sin ingredientes</span>
                                        @else
                                            <ul>
                                                @foreach ($detalles[$receta->rec_id] as $det)
                                                    <li>{{ $det->ing_nom }}: {{ $det->dre_can }} {{ $det->ing_um }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        @if (($presentaciones[$receta->rec_id] ?? collect())->isEmpty())
                                            <span class="empty-state">Sin presentaciones</span>
                                        @else
                                            <ul>
                                                @foreach ($presentaciones[$receta->rec_id] as $pres)
                                                    <li>{{ $pres->pro_nom }} ({{ $pres->tam_nom }}) factor {{ $pres->tam_factor }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty-state">No hay recetas registradas.</td>
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
