document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('form-filtros-usuarios');
    const buscar = document.getElementById('buscar');
    const rol = document.getElementById('rol');
    const estado = document.getElementById('estado');

    if (!formulario) {
        return;
    }

    let temporizador;

    function aplicarFiltros() {
        formulario.submit();
    }

    if (buscar) {
        buscar.addEventListener('input', function () {
            clearTimeout(temporizador);

            temporizador = setTimeout(function () {
                aplicarFiltros();
            }, 700);
        });

        buscar.addEventListener('search', function () {
            clearTimeout(temporizador);
            aplicarFiltros();
        });
    }

    if (rol) {
        rol.addEventListener('change', aplicarFiltros);
    }

    if (estado) {
        estado.addEventListener('change', aplicarFiltros);
    }
});