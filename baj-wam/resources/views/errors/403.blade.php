<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Acceso denegado | BAJ WAM</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        Swal.fire({
            icon: 'error',
            title: 'Acceso denegado',
            text: 'No tienes permisos para acceder a esta sección.',
            confirmButtonText: 'Aceptar',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((resultado) => {

            if (resultado.isConfirmed) {
                window.location.href = "{{ route('dashboard') }}";
            }

        });

    });
</script>

</body>
</html>