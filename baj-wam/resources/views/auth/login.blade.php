<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar sesión | BAJ WAM</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link
        href="{{ asset('assets/css/baj-wam.css') }}"
        rel="stylesheet"
    >
</head>

<body>

    <main class="login-page">

        <div class="login-card">

            <div class="row g-0">

                <div class="col-lg-6 login-brand-panel d-none d-lg-grid">
                    <img
                        src="{{ asset('assets/img/logo-baj-wam.png') }}"
                        alt="BAJ WAM Acupuntura"
                    >
                </div>

                <div class="col-lg-6 login-form-panel">

                    <a
                        class="text-decoration-none small"
                        href="{{ url('/') }}"
                    >
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver al sitio
                    </a>

                    <div class="mt-5 mb-4">
                        <h1 class="h2 fw-bold">
                            Bienvenido
                        </h1>

                        <p class="text-secondary">
                            Ingresa con tu cuenta autorizada.
                        </p>
                    </div>

                    @if (session('success'))
                        <div
                            class="alert alert-success alert-dismissible fade show"
                            role="alert"
                        >
                            <i class="bi bi-check-circle-fill me-2"></i>

                            {{ session('success') }}

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Cerrar"
                            ></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div
                            class="alert alert-danger alert-dismissible fade show"
                            role="alert"
                        >
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>

                            {{ session('error') }}

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Cerrar"
                            ></button>
                        </div>
                    @endif

                    <form
                        id="formLogin"
                        method="POST"
                        action="{{ route('login.autenticar') }}"
                        novalidate
                    >

                        @csrf

                        <div class="mb-3">

                            <label
                                class="form-label"
                                for="usuario"
                            >
                                Usuario o correo
                            </label>

                            <div class="input-group has-validation">

                                <span class="input-group-text bg-white">
                                    <i class="bi bi-person"></i>
                                </span>

                                <input
                                    class="form-control @error('usuario') is-invalid @enderror"
                                    id="usuario"
                                    name="usuario"
                                    type="text"
                                    maxlength="120"
                                    value="{{ old('usuario') }}"
                                    autocomplete="username"
                                    autofocus
                                >

                                <div
                                    class="invalid-feedback"
                                    id="error-usuario"
                                >
                                    @error('usuario')
                                        {{ $message }}
                                    @enderror
                                </div>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label
                                class="form-label"
                                for="password"
                            >
                                Contraseña
                            </label>

                            <div class="input-group has-validation">

                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input
                                    class="form-control @error('password') is-invalid @enderror"
                                    type="password"
                                    id="password"
                                    name="password"
                                    autocomplete="current-password"
                                >

                                <div
                                    class="invalid-feedback"
                                    id="error-password"
                                >
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </div>

                            </div>

                        </div>

{{--                         <div class="form-check mb-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="recordarme"
                                name="remember"
                                value="1"
                                @checked(old('remember'))
                            >

                            <label
                                class="form-check-label"
                                for="recordarme"
                            >
                                Mantener sesión iniciada
                            </label>

                        </div> --}}

                        <button
                            class="btn btn-brand btn-lg w-100"
                            type="submit"
                        >
                            Iniciar sesión
                        </button>

                    </form>

                    <p class="small text-secondary mt-4 mb-0">
                        Acceso exclusivo para personal autorizado de BAJ WAM.
                    </p>

                </div>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('assets/js/auth/login.js') }}"></script>

</body>

</html>