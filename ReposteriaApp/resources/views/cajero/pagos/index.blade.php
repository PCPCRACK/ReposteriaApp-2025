<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos - Cajero</title>
    <link rel="stylesheet" href="{{ asset('css/dashboardStyles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/BotonStyle.css') }}">
</head>
<body>
    <div class="container">
        @include('cajero.partials.sidebar')
        <div class="main-content">
            <div class="header">
                <div class="header-info">
                    <div class="header-title">Pagos</div>
                    <div class="header-subtitle">Registrar y consultar pagos</div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid-2-cols">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Registrar pago</div>
                        <div class="card-subtitle">Pedidos sin pago</div>
                    </div>
                    <form class="form-grid" method="POST" action="{{ route('cajero.pagos.store') }}">
                        @csrf
                        <label class="form-label">Pedido</label>
                        <select name="ped_id" class="form-input" required>
                            <option value="">Seleccione un pedido</option>
                            @foreach ($pedidosSinPago as $pedido)
                                <option value="{{ $pedido->ped_id }}">
                                    #{{ $pedido->ped_id }} - {{ trim(($pedido->cli_nom ?? '') . ' ' . ($pedido->cli_apellido ?? '')) ?: 'Cliente ocasional' }} | {{ $pedido->ped_est }} | ${{ number_format($pedido->ped_total, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>

                        <label class="form-label">Método de pago</label>
                        <select name="pag_metodo" class="form-input" required>
                            <option value="">Seleccione</option>
                            @foreach (['Efectivo','Tarjeta','Transferencia'] as $metodo)
                                <option value="{{ $metodo }}">{{ $metodo }}</option>
                            @endforeach
                        </select>

                        <div class="form-actions">
                            <button type="submit" class="primary-button">Registrar pago</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pagos registrados</div>
                    </div>
                    <div class="table-container">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>ID Pago</th>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Método</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pagos as $pago)
                                    <tr>
                                        <td>#{{ $pago->pag_id }}</td>
                                        <td>#{{ $pago->ped_id }}</td>
                                        <td>{{ trim(($pago->cli_nom ?? '') . ' ' . ($pago->cli_apellido ?? '')) ?: 'Cliente ocasional' }}</td>
                                        <td>{{ $pago->pag_metodo }}</td>
                                        <td>${{ number_format($pago->ped_total, 0, ',', '.') }}</td>
                                        <td>{{ $pago->pag_fec }} {{ $pago->pag_hora }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="empty-state">No hay pagos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
