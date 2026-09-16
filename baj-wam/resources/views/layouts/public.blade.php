<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') | BAJ WAM Acupuntura</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/baj-wam.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body class="public-body">
    <nav class="navbar navbar-expand-lg public-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand py-0" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo-baj-wam.png') }}" alt="BAJ WAM Acupuntura">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav" aria-controls="publicNav" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#inicio') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#servicios') }}">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#productos') }}">Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#cita') }}">Solicitar cita</a></li>
                    <li class="nav-item"><a class="btn btn-outline-brand ms-lg-2" href="{{ url('/login') }}"><i class="bi bi-person-lock me-2"></i>Acceso interno</a></li>
                </ul>
            </div>
        </div>
    </nav>

    @include('partials.flash-messages')
    @yield('content')

    <footer class="public-footer py-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>© {{ date('Y') }} BAJ WAM Acupuntura.</span>
            <span>Acupuntura · Medicina Natural · Terapia Nutricional</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/baj-wam.js') }}"></script>
    @stack('scripts')
</body>
</html>
