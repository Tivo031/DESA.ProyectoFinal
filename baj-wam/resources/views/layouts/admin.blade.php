<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') | BAJ WAM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/baj-wam.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="app-shell">
        <aside class="app-sidebar d-none d-lg-flex">
            <div class="app-brand">
                <a href="{{ url('/dashboard') }}" aria-label="Ir al dashboard">
                    <img src="{{ asset('assets/img/logo-baj-wam.png') }}" alt="BAJ WAM Acupuntura">
                </a>
            </div>

            <div class="sidebar-scroll">
                @include('partials.admin.menu')
            </div>

            <div class="sidebar-footer">
                <a class="btn btn-outline-brand w-100" href="{{ url('/') }}" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-2"></i>Ver sitio público
                </a>
            </div>
        </aside>

        <div class="app-main">
            @include('partials.admin.topbar')

            <main class="app-content">
                @include('partials.flash-messages')
                @yield('content')
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header border-bottom">
            <img src="{{ asset('assets/img/logo-baj-wam.png') }}" alt="BAJ WAM"
                style="width: 110px; height: 78px; object-fit: contain;">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body p-3">
            @include('partials.admin.menu')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/baj-wam.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/alertas.js') }}"></script>
    @stack('scripts')

</body>

</html>
