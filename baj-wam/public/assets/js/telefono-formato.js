
document.addEventListener('DOMContentLoaded', function () {

    // FORMATO DE TELÉFONOS
    document.querySelectorAll('input[data-formato-telefono]').forEach(function (telefono) {

        function aplicarFormato() {
            const numeros = telefono.value.replace(/\D/g, '').slice(0, 8);

            telefono.value = numeros.length > 4
                ? numeros.slice(0, 4) + '-' + numeros.slice(4)
                : numeros;
        }

        telefono.addEventListener('input', aplicarFormato);

        // FORMATEAR VALORES PRECARGADOS
        if (telefono.value) {
            aplicarFormato();
        }
    });
});
