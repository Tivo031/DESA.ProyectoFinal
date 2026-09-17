document.addEventListener("DOMContentLoaded", () => {

    // CONFIRMACIÓN DE DESACTIVACIÓN

    document
        .querySelectorAll("[data-confirm-delete]")
        .forEach((boton) => {

            boton.addEventListener(
                "click",
                async (evento) => {

                    evento.preventDefault();

                    const formulario =
                        boton.closest("form");

                    if (!formulario) {
                        return;
                    }

                    const mensaje =
                        boton.dataset.confirmDelete ||
                        "¿Deseas desactivar este registro?";

                    const resultado = await Swal.fire({

                        title: "¿Desactivar registro?",

                        text: mensaje,

                        icon: "warning",

                        showCancelButton: true,

                        confirmButtonColor: "#dc3545",

                        cancelButtonColor: "#6c757d",

                        confirmButtonText:
                            "Sí, desactivar",

                        cancelButtonText:
                            "Cancelar",

                        reverseButtons: true,

                    });

                    if (resultado.isConfirmed) {
                        formulario.submit();
                    }
                }
            );

        });


    // MENSAJES TEMPORALES

    document
        .querySelectorAll("[data-auto-dismiss]")
        .forEach((alerta) => {

            window.setTimeout(() => {

                const instancia =
                    bootstrap.Alert.getOrCreateInstance(
                        alerta
                    );

                instancia.close();

            }, 5000);

        });

});