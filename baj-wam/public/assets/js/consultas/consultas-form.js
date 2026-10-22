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

        if (campo.tomselect) {
            campo.tomselect.wrapper.classList.add("is-invalid");
        }

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

        if (campo.tomselect) {
            campo.tomselect.wrapper.classList.remove("is-invalid");
        }

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

                    const fila = boton.closest("[data-product-row]");

                    const filas = listaProductos.querySelectorAll(
                        "[data-product-row]"
                    );

                    if (filas.length === 1) {

                        fila.querySelectorAll("input, select").forEach((campo) => {

                            if (campo.tomselect) {
                                campo.tomselect.clear();
                            } else {
                                campo.value = "";
                            }

                            limpiarError(campo);
                        });

                        return;
                    }

                    // DESTRUIR BUSCADORES
                    fila.querySelectorAll("select[data-buscador]").forEach((select) => {

                        if (select.tomselect) {
                            select.tomselect.destroy();
                        }
                    });

                    fila.remove();
                };
            });
    };

    // AGREGAR PRODUCTO

    if (listaProductos && plantillaProducto && botonAgregarProducto) {

        // ÍNDICE PARA NUEVOS PRODUCTOS
        let siguienteIndice = Math.max(
            -1,
            ...Array.from(
                listaProductos.querySelectorAll(
                    'select[name^="productos["][name$="[id_producto]"]'
                )
            ).map((select) => {
                const coincidencia = select.name.match(/^productos\[(\d+)\]/);
                return coincidencia ? Number(coincidencia[1]) : -1;
            })
        ) + 1;

        botonAgregarProducto.addEventListener("click", () => {

            const html = plantillaProducto.innerHTML.replaceAll(
                "__INDEX__",
                siguienteIndice++
            );

            // AGREGAR FILA
            listaProductos.insertAdjacentHTML("beforeend", html);

            const nuevaFila = listaProductos.lastElementChild;

            // INICIALIZAR BUSCADOR
            if (nuevaFila && typeof inicializarBuscadores === "function") {
                inicializarBuscadores(nuevaFila);
            }

            // VERIFICAR INICIALIZACIÓN
            const selectProducto = nuevaFila?.querySelector(
                'select[name$="[id_producto]"]'
            );

            console.log(
                "Buscador inicializado:",
                Boolean(selectProducto?.tomselect)
            );

            // ASIGNAR BOTONES
            asignarBotonesEliminar();
        });
    }

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