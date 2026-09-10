<header class="app-topbar d-flex align-items-center px-3 px-lg-4">
    <button class="btn btn-light border d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="bi bi-list fs-5"></i>
    </button>

    <div class="me-auto">
        <div class="fw-bold">BAJ WAM Gestión</div>
        <small class="text-secondary d-none d-sm-inline">Sistema integral de gestión clínica</small>
    </div>

    <div class="dropdown">
        <button class="btn border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="user-avatar">{{ strtoupper(substr(auth()->user()?->nombres ?? 'A', 0, 1)) }}</span>
            <span class="text-start d-none d-md-block">
                <span class="d-block fw-bold small">{{ auth()->user()?->nombres ?? 'Administrador' }}</span>
                <span class="d-block text-secondary" style="font-size: .76rem;">{{ auth()->user()?->rol?->nombre ?? 'ADMINISTRADOR' }}</span>
            </span>
            <i class="bi bi-chevron-down small text-secondary"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                    </form>
                @else
                    <a class="dropdown-item text-danger" href="{{ url('/login') }}"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
                @endif
            </li>
        </ul>
    </div>
</header>
