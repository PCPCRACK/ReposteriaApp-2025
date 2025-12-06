<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido - Repostería</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/formStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/createPedidoStyles.css') }}">
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
                    <div class="header-title">Crear Nuevo Pedido</div>
                    <div class="header-subtitle">Completa el formulario para registrar un nuevo pedido.</div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('cajero.pedidos.index') }}" class="cancel-button">Cancelar</a>
                </div>
            </div>
            
            <div class="form-container" style="max-width: 900px; margin: 24px auto;">
                <form action="{{ route('cajero.pedidos.store') }}" method="POST" id="create-order-form"
                    data-clientes='@json($clientes)'
                    data-productos='@json($productos)'>
                    @csrf
                    <div class="form-container-grid">
                        <div class="form-group">
                            <label for="cli_cedula_input" class="form-label">Cliente</label>
                            <div class="input-group">
                                <input type="text" id="cli_cedula_input" class="form-input" placeholder="Digite la cédula del cliente" value="{{ $cli_cedula ?? '' }}">
                                <input type="hidden" name="cli_cedula" id="hidden_cli_cedula">
                                <button type="button" id="search-client" class="search-button">Buscar</button>
                            </div>
                            <div id="client-info"></div>
                        </div>
                        <div class="form-group">
                            <label for="emp_id" class="form-label">Cajero</label>
                            <select name="emp_id" id="emp_id" class="form-input" required>
                                <option value="">Seleccione un cajero</option>
                                @foreach ($cajeros as $cajero)
                                    <option value="{{ $cajero->emp_id }}">{{ $cajero->empleado->emp_nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Añadir Productos</label>
                        <div class="product-adder-grid">
                            <div class="form-group">
                                <label for="product-selector" class="form-label">Producto</label>
                                <select id="product-selector" class="form-input">
                                    <option value="">Seleccione un producto</option>
                                    @foreach ($productos as $producto)
                                        <option value="{{ $producto->pro_id }}">{{ $producto->pro_nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="size-selector" class="form-label">Tamaño</label>
                                <select id="size-selector" class="form-input" disabled>
                                    <option value="">Seleccione un tamaño</option>
                                </select>
                            </div>
                        </div>
                        <div class="quantity-adder">
                            <div class="form-group">
                                <label for="quantity" class="form-label">Cantidad</label>
                                <input type="number" id="quantity" class="form-input" value="1" min="1">
                            </div>
                            <button type="button" id="add-product" class="primary-action-button">Añadir</button>
                        </div>
                    </div>

                    <h3 class="summary-title">Resumen del Pedido</h3>
                    <div class="table-container" style="padding: 0;">
                        <table id="order-items-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="item-quantity">Cant.</th>
                                    <th class="item-price">P/U</th>
                                    <th class="item-subtotal">Subtotal</th>
                                    <th class="action-cell"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items will be added here by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <div class="summary-total">
                        <span class="total-label">Total:</span>
                        <span class="total-value" id="order-total">$0</span>
                    </div>

                    <div id="hidden-inputs-container"></div>

                    <div class="form-actions" style="margin-top: 24px;">
                        <button type="submit" class="primary-action-button">Guardar Pedido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Script starting.");
            const createOrderForm = document.getElementById('create-order-form');
            if (!createOrderForm) {
                console.error("Form 'create-order-form' not found.");
                return;
            }

            let clientesData = [];
            let productosData = [];

            try {
                clientesData = JSON.parse(createOrderForm.dataset.clientes || '[]');
                console.log("clientesData loaded:", clientesData);
            } catch (e) {
                console.error("Error parsing clientesData from data attribute:", e);
            }

            try {
                productosData = JSON.parse(createOrderForm.dataset.productos || '[]');
                console.log("productosData loaded:", productosData);
            } catch (e) {
                console.error("Error parsing productosData from data attribute:", e);
            }

            // Client Search Elements
            const clientSearchInput = document.getElementById('cli_cedula_input');
            const searchClientBtn = document.getElementById('search-client');
            const clientInfoDiv = document.getElementById('client-info');
            const hiddenCliCedulaInput = document.getElementById('hidden_cli_cedula');

            function findClient() {
                const cedula = clientSearchInput.value.trim();
                console.log('Searching for client with cedula:', cedula);
                if (cedula === "") {
                    clientInfoDiv.textContent = '';
                    hiddenCliCedulaInput.value = '';
                    return;
                }

                const client = clientesData.find(c => String(c.cli_cedula) === String(cedula));
                if (client) {
                    clientInfoDiv.innerHTML = `<strong>Cliente:</strong> ${client.cli_nom} ${client.cli_apellido}`;
                    clientInfoDiv.style.color = '#166534';
                    hiddenCliCedulaInput.value = client.cli_cedula;
                    console.log('Client found:', client);
                } else {
                    clientInfoDiv.textContent = 'Cliente no encontrado.';
                    clientInfoDiv.style.color = '#991B1B';
                    hiddenCliCedulaInput.value = '';
                    if (cedula.length === 10 && /^\d+$/.test(cedula)) {
                        if (confirm('Cliente no encontrado. ¿Desea registrar este nuevo cliente?')) {
                            window.location.href = `{{ route('admin.clientes.create') }}?cedula=${cedula}&from=pedidos`;
                        }
                    }
                    console.log('Client not found.');
                }
            }

            // Event Listeners for Client Search
            searchClientBtn.addEventListener('click', findClient);
            clientSearchInput.addEventListener('blur', findClient);
            if (clientSearchInput.value) { // Trigger on page load if pre-filled
                findClient();
            }

            // Product Selection Elements
            const productSelector = document.getElementById('product-selector');
            const sizeSelector = document.getElementById('size-selector');
            const quantityInput = document.getElementById('quantity');
            const addProductBtn = document.getElementById('add-product');
            const orderItemsTableBody = document.querySelector('#order-items-table tbody');
            const orderTotalCell = document.getElementById('order-total');
            const hiddenInputsContainer = document.getElementById('hidden-inputs-container');

            let orderItems = [];

            function populateSizes() {
                const selectedProductId = productSelector.value;
                sizeSelector.innerHTML = '<option value="">Seleccione un tamaño</option>';
                sizeSelector.disabled = true;

                if (selectedProductId) {
                    const selectedProduct = productosData.find(p => String(p.pro_id) === String(selectedProductId));
                    if (selectedProduct && selectedProduct.presentaciones && selectedProduct.presentaciones.length > 0) {
                        sizeSelector.disabled = false;
                        selectedProduct.presentaciones.forEach(presentacion => {
                            const option = document.createElement('option');
                            option.value = presentacion.prp_id;
                            option.textContent = `${presentacion.tamano.tam_nom} - $${presentacion.prp_precio.toLocaleString('es-CO')}`;
                            option.dataset.price = presentacion.prp_precio;
                            option.dataset.name = `${selectedProduct.pro_nom} (${presentacion.tamano.tam_nom})`;
                            sizeSelector.appendChild(option);
                        });
                        console.log('Sizes populated for product:', selectedProductId);
                    } else {
                        console.log('Selected product or its presentations not found/empty for product:', selectedProductId);
                    }
                }
            }

            // Event Listener for Product Selection
            productSelector.addEventListener('change', populateSizes);

            function addProductToOrder() {
                const selectedSizeOption = sizeSelector.options[sizeSelector.selectedIndex];
                if (!selectedSizeOption || !selectedSizeOption.value) {
                    alert('Por favor, seleccione un producto y un tamaño.');
                    console.log('Add product failed: No product or size selected.');
                    return;
                }

                const prp_id = selectedSizeOption.value;
                const name = selectedSizeOption.dataset.name;
                const price = parseFloat(selectedSizeOption.dataset.price);
                const quantity = parseInt(quantityInput.value);

                if (isNaN(quantity) || quantity < 1) {
                    alert('Por favor, introduce una cantidad válida.');
                    console.log('Add product failed: Invalid quantity:', quantity);
                    return;
                }

                const existingItemIndex = orderItems.findIndex(item => String(item.id) === String(prp_id));
                if (existingItemIndex > -1) {
                    orderItems[existingItemIndex].quantity += quantity;
                    console.log('Updated existing item:', orderItems[existingItemIndex]);
                } else {
                    orderItems.push({ id: prp_id, name: name, quantity: quantity, price: price });
                    console.log('Added new item:', { id: prp_id, name: name, quantity: quantity, price: price });
                }
                
                renderTable();
                updateTotal();
                // Reset product selection for next item
                productSelector.value = "";
                populateSizes(); // Clear sizes
                quantityInput.value = 1;
            }

            // Event Listener for Add Product Button
            addProductBtn.addEventListener('click', addProductToOrder);

            function renderTable() {
                orderItemsTableBody.innerHTML = '';
                if (orderItems.length === 0) {
                    orderItemsTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No hay productos en el pedido.</td></tr>';
                    return;
                }
                orderItems.forEach(item => {
                    const subtotal = item.quantity * item.price;
                    const row = `
                        <tr>
                            <td class="item-name">${item.name}</td>
                            <td class="item-quantity">${item.quantity}</td>
                            <td class="item-price">$${item.price.toLocaleString('es-CO')}</td>
                            <td class="item-subtotal">$${subtotal.toLocaleString('es-CO')}</td>
                            <td class="action-cell">
                                <button type="button" class="action-button delete-button remove-item" data-id="${item.id}">X</button>
                            </td>
                        </tr>
                    `;
                    orderItemsTableBody.innerHTML += row;
                });
                console.log('Order table rendered.');
            }

            function updateTotal() {
                const total = orderItems.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                orderTotalCell.textContent = `$${total.toLocaleString('es-CO')}`;
                console.log('Order total updated:', total);
            }

            // Event Listener for Removing Items
            orderItemsTableBody.addEventListener('click', function (e) {
                if (e.target.closest('.delete-button')) {
                    const idToRemove = e.target.closest('.delete-button').dataset.id;
                    orderItems = orderItems.filter(item => String(item.id) !== String(idToRemove));
                    renderTable();
                    updateTotal();
                    console.log('Item removed with ID:', idToRemove);
                }
            });

            // Form Submission Handler
            createOrderForm.addEventListener('submit', function(e) {
                console.log('Form submission attempted.');
                if (!hiddenCliCedulaInput.value) {
                    e.preventDefault();
                    alert('Debes seleccionar un cliente válido.');
                    console.log('Submission failed: No valid client selected.');
                    return;
                }
                if (orderItems.length === 0) {
                    e.preventDefault();
                    alert('Debes añadir al menos un producto al pedido.');
                    console.log('Submission failed: No items in order.');
                    return;
                }
                // Clear previous hidden inputs and generate new ones
                hiddenInputsContainer.innerHTML = '';
                orderItems.forEach((item, index) => {
                    hiddenInputsContainer.insertAdjacentHTML('beforeend', `
                        <input type="hidden" name="items[${index}][prp_id]" value="${item.id}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        <input type="hidden" name="items[${index}][price]" value="${item.price}">
                    `);
                });
                console.log('Hidden inputs generated for submission:', orderItems);
            });

            // Initial render of the table
            renderTable();
            console.log("Script finished.");
        });
    </script>