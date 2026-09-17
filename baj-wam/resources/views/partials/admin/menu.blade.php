<nav class="sidebar-nav nav flex-column" aria-label="Menú principal">
    <div class="sidebar-label">Principal</div>
    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
    </a>

    <div class="sidebar-label">Atención clínica</div>
    <a class="nav-link {{ request()->is('pacientes*') ? 'active' : '' }}" href="{{ url('/pacientes') }}">
        <i class="bi bi-people-fill"></i><span>Pacientes</span>
    </a>
    <a class="nav-link {{ request()->is('citas*') ? 'active' : '' }}" href="{{ url('/citas') }}">
        <i class="bi bi-calendar2-check-fill"></i><span>Citas</span>
    </a>
    <a class="nav-link {{ request()->is('consultas*') ? 'active' : '' }}" href="{{ url('/consultas') }}">
        <i class="bi bi-clipboard2-pulse-fill"></i><span>Consultas</span>
    </a>

    <div class="sidebar-label">Productos</div>
    <a class="nav-link {{ request()->is('productos*') ? 'active' : '' }}" href="{{ url('/productos') }}">
        <i class="bi bi-capsule-pill"></i><span>Productos naturales</span>
    </a>
    <a class="nav-link {{ request()->is('inventario*') ? 'active' : '' }}" href="{{ url('/inventario') }}">
        <i class="bi bi-box-seam-fill"></i><span>Inventario</span>
    </a>
    @can('usuarios.ver')
        <div class="sidebar-label">Administración</div>

        <a class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}" href="{{ url('/usuarios') }}">
            <i class="bi bi-person-gear"></i><span>Usuarios</span>
        </a>
    @endcan
    @can('roles.ver')
        <a class="nav-link {{ request()->is('roles.*') ? 'active' : '' }}" href="{{ url('/roles') }}">
            <i class="bi bi-shield-lock"></i><span>Roles</span>
        </a>
    @endcan
    @can('permisos.ver')
        <a class="nav-link {{ request()->is('permisos*') ? 'active' : '' }}" href="{{ url('/permisos') }}">

            <i class="bi bi-key-fill"></i>
            <span>Permisos</span>

        </a>
    @endcan
</nav>
