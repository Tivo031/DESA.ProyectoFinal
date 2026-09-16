<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cambiar contraseña | BAJ WAM</title>

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

    <main class="container py-5">

        <div class="row justify-content-center">

            <div class="col-12 col-md-8 col-lg-6">

                <div class="card bw-card">

                    <div class="card-body p-4 p-lg-5">

                        <div class="text-center mb-4">

                            <i class="bi bi-shield-lock fs-1 text-warning"></i>

                            <h1 class="h3 mt-3">
                                Cambio de contraseña requerido
                            </h1>

                            <p class="text-secondary mb-0">
                                Por seguridad, debes establecer una nueva contraseña antes de continuar.
                            </p>

                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('password.actualizar') }}"
                            id="formCambioPassword"
                            novalidate
                        >

                            @csrf
                            @method('PUT')

                            {{-- USUARIO AUTENTICADO --}}

                            <input
                                type="text"
                                name="username"
                                value="{{ auth()->user()->usuario }}"
                                autocomplete="username"
                                class="visually-hidden"
                                tabindex="-1"
                                aria-hidden="true"
                                readonly
                            >

                            {{-- NUEVA CONTRASEÑA --}}

                            <div class="mb-3">

                                <label
                                    for="password"
                                    class="form-label"
                                >
                                    Nueva contraseña
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    minlength="10"
                                    autocomplete="new-password"
                                >

                                <div
                                    class="invalid-feedback"
                                    id="error-password"
                                >
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </div>

                                <div class="form-text">
                                    Mínimo 10 caracteres, incluyendo mayúscula, minúscula, número y símbolo.
                                </div>

                            </div>

                            {{-- CONFIRMAR CONTRASEÑA --}}

                            <div class="mb-4">

                                <label
                                    for="password_confirmation"
                                    class="form-label"
                                >
                                    Confirmar contraseña
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    minlength="10"
                                    autocomplete="new-password"
                                >

                                <div
                                    class="invalid-feedback"
                                    id="error-password_confirmation"
                                ></div>

                            </div>

                            {{-- GUARDAR --}}

                            <div class="d-grid gap-2">

                                <button
                                    type="submit"
                                    class="btn btn-brand"
                                >
                                    <i class="bi bi-shield-check me-2"></i>
                                    Guardar nueva contraseña
                                </button>

                            </div>

                        </form>

                        {{-- CERRAR SESIÓN --}}

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            class="mt-3"
                        >
                            @csrf

                            <div class="d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-light border"
                                >
                                    Cerrar sesión
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </main>

</body>

</html>