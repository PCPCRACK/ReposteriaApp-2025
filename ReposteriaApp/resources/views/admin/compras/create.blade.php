<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Compra</title>
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        @include('admin.partials.sidebar')

        <div class="main-content">
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Registrar Compra</div>
                    <div class="header-subtitle">Captura proveedor, fecha y detalle de ingredientes</div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.compras.index') }}" class="cancel-button">Cancelar</a>
                </div>
            </div>

            <div class="card">
                <form method="POST" action="{{ route('admin.compras.store') }}" id="compra-form">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Proveedor</label>
                            <select name="prov_id" class="form-input" required>
                                <option value="">Seleccione</option>
                                @foreach ($proveedores as $prov)
                                    <option value="{{ $prov->prov_id }}">{{ $prov->prov_nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha y hora</label>
                            <input type="datetime-local" name="com_fec" class="form-input" required>
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

        let items = [];

        function render() {
            tbody.innerHTML = '';
            hiddenItems.innerHTML = '';
            let total = 0;
            items.forEach((item, idx) => {
                const subtotal = item.cantidad * item.precio;
                total += subtotal;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.nombre}</td>
                    <td>${item.cantidad}</td>
                    <td>$${item.precio.toLocaleString('es-CO')}</td>
                    <td>$${subtotal.toLocaleString('es-CO')}</td>
                    <td><button type=\"button\" data-idx=\"${idx}\" class=\"filter-button secondary\">Quitar</button></td>
                `;
                tbody.appendChild(tr);

                hiddenItems.insertAdjacentHTML('beforeend', `
                    <input type=\"hidden\" name=\"items[${idx}][ing_id]\" value=\"${item.ing_id}\">
                    <input type=\"hidden\" name=\"items[${idx}][dco_can]\" value=\"${item.cantidad}\">
                    <input type=\"hidden\" name=\"items[${idx}][dco_pre]\" value=\"${item.precio}\">
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
            items.push({ ing_id: ingId, nombre: ingName, cantidad, precio });
            render();
            ingSelector.value = '';
            cantInput.value = '';
            precioInput.value = '';
        });
    </script>
</body>
</html>
