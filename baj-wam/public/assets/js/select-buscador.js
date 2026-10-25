// SELECTS CON BUSCADOR
function inicializarBuscadores(contenedor = document) {

    if (typeof TomSelect === 'undefined') {
        return;
    }

    contenedor.querySelectorAll('select[data-buscador]').forEach(function (select) {

        if (select.tomselect) {
            return;
        }

        const placeholder = select.getAttribute('data-placeholder')
            || select.getAttribute('placeholder')
            || select.querySelector('option[value=""]')?.textContent.trim()
            || 'Selecciona una opción';

        new TomSelect(select, {
            create: false,
            maxItems: 1,
            allowEmptyOption: false,
            placeholder: placeholder,
            searchField: ['text'],
            sortField: [],
            plugins: {
                clear_button: {
                    title: 'Limpiar selección'
                }
            },
            render: {
                no_results: function () {
                    return '<div class="no-results p-2">Sin resultados</div>';
                }
            }
        });
    });
}

// INICIALIZAR AL CARGAR LA PÁGINA
document.addEventListener('DOMContentLoaded', function () {
    inicializarBuscadores();
});