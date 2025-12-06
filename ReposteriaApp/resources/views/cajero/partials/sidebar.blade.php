<div class="sidebar">
    <div class="logo">
        <div class="logo-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L13.09 8.26L20 9.27L15 14.14L16.18 21.02L12 17.77L7.82 21.02L9 14.14L4 9.27L10.91 8.26L12 2Z" fill="#1F2937"/>
            </svg>
        </div>
        <div class="logo-text">
            <div>reposteria</div>
            <div class="logo-subtitle">CAJERO</div>
        </div>
    </div>
    <div class="nav">
        <div class="nav-item">
            <a href="{{ route('cajero.dashboard') }}" class="nav-link">
                <div class="nav-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 13H11V3H3V13ZM3 21H11V15H3V21ZM13 21H21V11H13V21ZM13 3V9H21V3H13Z" fill="#1F2937"/>
                    </svg>
                </div>
                <div class="nav-text">Dashboard</div>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('cajero.productos.index') }}" class="nav-link">
                <div class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 4H10V0H6V4H2L0 6V14C0 14.5304 0.210714 15.0391 0.585786 15.4142C0.960859 15.7893 1.46957 16 2 16H14C14.5304 16 15.0391 15.7893 15.4142 15.4142C15.7893 15.0391 16 14.5304 16 14V6L14 4ZM8 2H8.8L9.2 4H8V2ZM2 6H14V14H2V6Z" fill="#4B5563"/>
                    </svg>
                </div>
                <div class="nav-text">Productos</div>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('cajero.pedidos.index') }}" class="nav-link">
                <div class="nav-icon">
                    <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 0H2C0.9 0 0 0.9 0 2V14C0 15.1 0.9 16 2 16H10C11.1 16 12 15.1 12 14V2C12 0.9 11.1 0 10 0ZM10 14H2V2H10V14ZM4 10H6V12H4V10ZM4 7H6V9H4V7ZM4 4H6V6H4V4ZM8 10H10V12H8V10ZM8 7H10V9H8V7ZM8 4H10V6H8V4Z" fill="#4B5563"/>
                    </svg>
                </div>
                <div class="nav-text">Pedidos</div>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('cajero.pagos.index') }}" class="nav-link">
                <div class="nav-icon">
                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0V2H18V0H0ZM0 7H18V5H0V7ZM0 12H18V10H0V12Z" fill="#4B5563"/>
                    </svg>
                </div>
                <div class="nav-text">Pagos</div>
            </a>
        </div>
    </div>
</div>
