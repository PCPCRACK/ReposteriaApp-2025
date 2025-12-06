<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Compra</title>
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        @include('admin.partials.sidebar')

        <div class="main-content">
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Editar Compra #{{ $compra->com_id }}</div>
                    <div class="header-subtitle">Actualiza proveedor, fecha y detalle</div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.compras.index') }}" class="cancel-button">Cancelar</a>
                </div>
            </div>

            <div class="card">
                <form method="POST" action="{{ route('admin.compras.update', $compra->com_id) }}" id="compra-form">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Proveedor</label>
                            <select name="prov_id" class="form-input" required>
                                @foreach ($proveedores as $prov)
                                    <option value="{{ $prov->prov_id }}" @if($prov->prov_id == $compra->prov_id) selected @endif>{{ $prov->prov_nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha y hora</label>
                            <input type="datetime-local" name="com_fec" class="form-input" value="{{ \Carbon\Carbon::parse($compra->com_fec)->format('Y-m-d\\TH:i') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Detalle de compra</label>
                        <div class="product-adder-grid">
                            <select id="ing-selector" class="form-input">
                                <option value="">Ingrediente</option>
                                @foreach ($ingredientes as $ing)
                                    <option value="{{ $ing->ing_id }}">{{ $ing->ing_nom }}</option>
                                @endforeach
                            </select>
                            <input type="number" id="cant-input" class="form-input" placeholder="Cantidad" min="0.01" step="0.01">
                            <input type="number" id="precio-input" class="form-input" placeholder="Precio unitario" min="0" step="0.01">
                            <button type="button" id="add-detalle" class="primary-action-button">Agregar</button>
                        </div>
                    </div>

                    <div class="table-container" style="padding:0;">
                        <table class="inventory-table" id="detalle-table">
                            <thead>
                                <tr>
                                    <th>Ingrediente</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="summary-total">
                        <span class="total-label">Total:</span>
                        <span class="total-value" id="total-compra">$0</span>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="confirmar" value="1"> Confirmar y actualizar stock ahora</label>
                    </div>

                    <div id="hidden-items"></div>

                    <div class="form-actions">
                        <button type="submit" class="primary-action-button">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const ingSelector = document.getElementById('ing-selector');
        const cantInput = document.getElementById('cant-input');
        const precioInput = document.getElementById('precio-input');
        const addDetalleBtn = document.getElementById('add-detalle');
        const tbody = document.querySelector('#detalle-table tbody');
        const totalCompra = document.getElementById('total-compra');
        const hiddenItems = document.getElementById('hidden-items');

        let items = @json($detalles);

        function render() {
            tbody.innerHTML = '';
            hiddenItems.innerHTML = '';
            let total = 0;
            items.forEach((item, idx) => {
                const subtotal = item.dco_can * item.dco_pre;
                total += subtotal;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.ing_nom ?? ''}</td>
                    <td>${item.dco_can}</td>
                    <td>$${Number(item.dco_pre).toLocaleString('es-CO')}</td>
                    <td>$${subtotal.toLocaleString('es-CO')}</td>
                    <td><button type=\"button\" data-idx=\"${idx}\" class=\"filter-button secondary\">Quitar</button></td>
                `;
                tbody.appendChild(tr);

                hiddenItems.insertAdjacentHTML('beforeend', `
                    <input type=\"hidden\" name=\"items[${idx}][ing_id]\" value=\"${item.ing_id}\">
                    <input type=\"hidden\" name=\"items[${idx}][dco_can]\" value=\"${item.dco_can}\">
                    <input type=\"hidden\" name=\"items[${idx}][dco_pre]\" value=\"${item.dco_pre}\">
                `);
            });
            totalCompra.textContent = '$' + total.toLocaleString('es-CO');

            tbody.querySelectorAll('button[data-idx]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const i = parseInt(btn.dataset.idx);
                    items.splice(i,1);
                    render();
                });
            });
        }

        addDetalleBtn.addEventListener('click', () => {
            const ingId = ingSelector.value;
            const ingName = ingSelector.options[ingSelector.selectedIndex]?.text;
            const cantidad = parseFloat(cantInput.value);
            const precio = parseFloat(precioInput.value);
            if (!ingId || isNaN(cantidad) || cantidad <= 0 || isNaN(precio) || precio < 0) {
                alert('Ingrese ingrediente, cantidad y precio válidos');
                return;
            }
            items.push({ ing_id: ingId, ing_nom: ingName, dco_can: cantidad, dco_pre: precio });
            render();
            ingSelector.value = '';
            cantInput.value = '';
            precioInput.value = '';
        });

        render();
    </script>
</body>
</html>
