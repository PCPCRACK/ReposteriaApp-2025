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
                    <div class="header-subtitle">Catálogo de presentaciones y precios</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="justify-content: space-between; align-items: center;">
                    <div>
                        <div class="card-title">Catálogo</div>
                        <div class="card-subtitle">Agrupado por producto</div>
                    </div>
                    <div class="filters-row" style="gap: 8px;">
                        <input id="product-filter" type="search" class="form-input" placeholder="Buscar producto...">
                    </div>
                </div>
                <div class="table-container compact">
                    <div class="accordion-list" id="product-accordion">
                        @forelse ($productos as $proId => $lista)
                            @php $first = $lista->first(); @endphp
                            <details class="accordion-item" data-name="{{ strtolower($first->pro_nom) }}">
                                <summary>
                                    <div class="accordion-title">{{ $first->pro_nom }}</div>
                                    <div class="accordion-meta">{{ $lista->count() }} presentaciones</div>
                                </summary>
                                <div class="accordion-body">
                                    <table class="inventory-table">
                                        <thead>
                                            <tr>
                                                <th>Tamaño</th>
                                                <th>Precio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lista as $item)
                                                <tr>
                                                    <td>{{ $item->tam_nom ?? 'N/D' }}</td>
                                                    <td>${{ number_format($item->prp_precio, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @empty
                            <div class="empty-state">No hay productos disponibles.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .accordion-list { display: flex; flex-direction: column; gap: 8px; }
        .accordion-item { border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; background: #fff; }
        .accordion-item[open] { box-shadow: 0 6px 14px rgba(0,0,0,0.05); }
        .accordion-item summary { list-style: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600; }
        .accordion-item summary::-webkit-details-marker { display: none; }
        .accordion-title { font-size: 15px; }
        .accordion-meta { font-size: 12px; color: #6b7280; }
        .accordion-body { margin-top: 10px; }
    </style>
    <script>
        (function(){
            const input = document.getElementById('product-filter');
            const items = Array.from(document.querySelectorAll('#product-accordion details'));
            if (!input) return;
            input.addEventListener('input', () => {
                const q = input.value.toLowerCase();
                items.forEach(item => {
                    const name = item.dataset.name || '';
                    item.style.display = name.includes(q) ? '' : 'none';
                });
            });
        })();
    </script>
</body>
</html>
