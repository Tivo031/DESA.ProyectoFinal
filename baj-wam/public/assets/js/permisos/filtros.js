document.addEventListener('DOMContentLoaded', function () {

    const formulario =
        document.getElementById('form-filtros-permisos');

    const buscar =
        document.getElementById('buscar');

    const modulo =
        document.getElementById('modulo');

    const estado =
        document.getElementById('estado');

    if (!formulario) {
        return;
    }

    let temporizador;

    function aplicarFiltros() {
        formulario.submit();
    }

    // BÚSQUEDA

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

    // MÓDULO

    if (modulo) {
        modulo.addEventListener(
            'change',
            aplicarFiltros
        );
    }

    // ESTADO

    if (estado) {
        estado.addEventListener(
            'change',
            aplicarFiltros
        );
    }

});