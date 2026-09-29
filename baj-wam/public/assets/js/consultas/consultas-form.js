document.addEventListener("DOMContentLoaded", () => {

    const formulario = document.getElementById("formConsulta");

    if (!formulario) {
        return;
    }

    const campos = {
        id_cita: document.getElementById("id_cita"),
        fecha_consulta: document.getElementById("fecha_consulta"),
        motivo_consulta: document.getElementById("motivo_consulta"),
        observaciones: document.getElementById("observaciones"),
        diagnostico: document.getElementById("diagnostico"),
        tratamiento_realizado: document.getElementById(
            "tratamiento_realizado"
        ),
        recomendaciones: document.getElementById("recomendaciones"),
    };

    const listaProductos = document.getElementById(
        "recommended-products-list"
    );

    const plantillaProducto = document.getElementById(
        "product-row-template"
    );

    const botonAgregarProducto = document.getElementById(
        "add-product-row"
    );


    // ERRORES

    const mostrarError = (campo, mensaje) => {

        if (!campo) {
            return;
        }

        campo.classList.add("is-invalid");

        const error = document.getElementById(
            `error-${campo.id}`
        );

        if (error) {
            error.textContent = mensaje;
        }
    };


    const limpiarError = (campo) => {

        if (!campo) {
            return;
        }

        campo.classList.remove("is-invalid");

        const error = document.getElementById(
            `error-${campo.id}`
        );

        if (error) {
            error.textContent = "";
        }
    };


    // VALIDACIÓN DEL FORMULARIO

    const validarFormulario = () => {

        let valido = true;

        Object.values(campos).forEach((campo) => {

            if (campo) {
                limpiarError(campo);
            }

        });


        // CITA

        if (campos.id_cita.value === "") {

            mostrarError(
                campos.id_cita,
                "Debes seleccionar una cita."
            );

            valido = false;
        }


        // FECHA

        if (campos.fecha_consulta.value === "") {

            mostrarError(
                campos.fecha_consulta,
                "La fecha de consulta es obligatoria."
            );

            valido = false;
        }


        // MOTIVO

        const motivo = campos.motivo_consulta.value.trim();

        if (motivo === "") {

            mostrarError(
                campos.motivo_consulta,
                "El motivo de consulta es obligatorio."
            );

            valido = false;
        }


        // TRATAMIENTO

        const tratamiento =
            campos.tratamiento_realizado.value.trim();

        if (tratamiento === "") {

            mostrarError(
                campos.tratamiento_realizado,
                "El tratamiento realizado es obligatorio."
            );

            valido = false;
        }


        // PRODUCTOS

        const productosSeleccionados = new Set();

        if (listaProductos) {

            const filas = listaProductos.querySelectorAll(
                "[data-product-row]"
            );

            filas.forEach((fila) => {

                const producto = fila.querySelector(
                    'select[name*="[id_producto]"]'
                );

                const cantidad = fila.querySelector(
                    'input[name*="[cantidad_recomendada]"]'
                );

                const indicaciones = fila.querySelector(
                    'input[name*="[indicaciones]"]'
                );

                if (!producto || producto.value === "") {
                    return;
                }

                limpiarError(producto);

                if (productosSeleccionados.has(producto.value)) {

                    mostrarError(
                        producto,
                        "Este producto ya fue agregado."
                    );

                    valido = false;

                } else {

                    productosSeleccionados.add(
                        producto.value
                    );
                }

                if (
                    cantidad &&
                    cantidad.value !== "" &&
                    Number(cantidad.value) <= 0
                ) {

                    mostrarError(
                        cantidad,
                        "La cantidad debe ser mayor que cero."
                    );

                    valido = false;
                }

                if (
                    indicaciones &&
                    indicaciones.value.length > 500
                ) {

                    mostrarError(
                        indicaciones,
                        "Las indicaciones no pueden superar los 500 caracteres."
                    );

                    valido = false;
                }

            });
        }

        return valido;
    };


    // LIMPIAR ERRORES

    Object.values(campos).forEach((campo) => {

        if (!campo) {
            return;
        }

        campo.addEventListener("input", () => {
            limpiarError(campo);
        });

        campo.addEventListener("change", () => {
            limpiarError(campo);
        });

    });


    // QUITAR PRODUCTO

    const asignarBotonesEliminar = () => {

        if (!listaProductos) {
            return;
        }

        listaProductos
            .querySelectorAll(".remove-product-row")
            .forEach((boton) => {

                boton.onclick = () => {

                    const filas =
                        listaProductos.querySelectorAll(
                            "[data-product-row]"
                        );

                    if (filas.length === 1) {

                        filas[0]
                            .querySelectorAll(
                                "input, select"
                            )
                            .forEach((campo) => {
                                campo.value = "";
                                limpiarError(campo);
                            });

                        return;
                    }

                    boton
                        .closest("[data-product-row]")
                        .remove();
                };
            });
    };


    // AGREGAR PRODUCTO

    if (
        listaProductos &&
        plantillaProducto &&
        botonAgregarProducto
    ) {

        botonAgregarProducto.addEventListener(
            "click",
            () => {

                const indice =
                    listaProductos.querySelectorAll(
                        "[data-product-row]"
                    ).length;

                const html =
                    plantillaProducto.innerHTML.replaceAll(
                        "__INDEX__",
                        indice
                    );

                listaProductos.insertAdjacentHTML(
                    "beforeend",
                    html
                );

                asignarBotonesEliminar();
            }
        );
    }


    // PRODUCTOS EXISTENTES

    asignarBotonesEliminar();


    // ENVÍO DEL FORMULARIO

    formulario.addEventListener(
        "submit",
        async (evento) => {

            evento.preventDefault();

            if (!validarFormulario()) {

                const primerError =
                    formulario.querySelector(
                        ".is-invalid"
                    );

                if (primerError) {
                    primerError.focus();
                }

                return;
            }

            const resultado = await Swal.fire({

                title: "¿Registrar consulta?",

                text:
                    "La consulta quedará registrada en el historial clínico del paciente.",

                icon: "question",

                showCancelButton: true,

                confirmButtonColor: "#198754",

                cancelButtonColor: "#6c757d",

                confirmButtonText:
                    "Sí, registrar consulta",

                cancelButtonText: "Cancelar",

                reverseButtons: true,
            });

            if (resultado.isConfirmed) {
                formulario.submit();
            }
        }
    );

});