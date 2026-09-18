document.addEventListener("DOMContentLoaded", () => {

    const formulario = document.getElementById("formPermiso");

    if (!formulario) {
        return;
    }

    const modo = formulario.dataset.modo;

    const campos = {
        codigo: document.getElementById("codigo"),
        nombre: document.getElementById("nombre"),
        modulo: document.getElementById("modulo"),
        descripcion: document.getElementById("descripcion"),
    };


    // EXPRESIONES REGULARES

    const regexCodigo = /^[a-z0-9._-]+$/;

    const regexModulo = /^[\p{L}0-9 _-]+$/u;


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


        // CÓDIGO

        if (modo === "crear" && campos.codigo) {

            const codigo = campos.codigo.value.trim();

            if (codigo === "") {

                mostrarError(
                    campos.codigo,
                    "El código del permiso es obligatorio."
                );

                valido = false;

            } else if (codigo.length < 3) {

                mostrarError(
                    campos.codigo,
                    "El código debe tener al menos 3 caracteres."
                );

                valido = false;

            } else if (codigo.length > 100) {

                mostrarError(
                    campos.codigo,
                    "El código no puede superar los 100 caracteres."
                );

                valido = false;

            } else if (!regexCodigo.test(codigo)) {

                mostrarError(
                    campos.codigo,
                    "El código únicamente puede contener letras minúsculas, números, puntos, guiones y guiones bajos."
                );

                valido = false;
            }
        }


        // NOMBRE

        const nombre = campos.nombre.value.trim();

        if (nombre === "") {

            mostrarError(
                campos.nombre,
                "El nombre del permiso es obligatorio."
            );

            valido = false;

        } else if (nombre.length < 3) {

            mostrarError(
                campos.nombre,
                "El nombre del permiso debe tener al menos 3 caracteres."
            );

            valido = false;

        } else if (nombre.length > 100) {

            mostrarError(
                campos.nombre,
                "El nombre del permiso no puede superar los 100 caracteres."
            );

            valido = false;
        }


        // MÓDULO

        const modulo = campos.modulo.value.trim();

        if (modulo === "") {

            mostrarError(
                campos.modulo,
                "El módulo es obligatorio."
            );

            valido = false;

        } else if (modulo.length < 3) {

            mostrarError(
                campos.modulo,
                "El módulo debe tener al menos 3 caracteres."
            );

            valido = false;

        } else if (modulo.length > 50) {

            mostrarError(
                campos.modulo,
                "El módulo no puede superar los 50 caracteres."
            );

            valido = false;

        } else if (!regexModulo.test(modulo)) {

            mostrarError(
                campos.modulo,
                "El módulo únicamente puede contener letras, números, espacios, guiones y guiones bajos."
            );

            valido = false;
        }


        // DESCRIPCIÓN

        const descripcion =
            campos.descripcion.value.trim();

        if (descripcion === "") {

            mostrarError(
                campos.descripcion,
                "La descripción es obligatoria."
            );

            valido = false;

        } else if (descripcion.length > 200) {

            mostrarError(
                campos.descripcion,
                "La descripción no puede superar los 200 caracteres."
            );

            valido = false;
        }


        return valido;
    };


    // LIMPIAR ERRORES

    Object.values(campos).forEach((campo) => {

        if (!campo || campo.disabled) {
            return;
        }

        campo.addEventListener("input", () => {
            limpiarError(campo);
        });

        campo.addEventListener("change", () => {
            limpiarError(campo);
        });

    });


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

            const esEdicion =
                modo === "editar";

            const resultado = await Swal.fire({

                title: esEdicion
                    ? "¿Guardar cambios?"
                    : "¿Crear permiso?",

                text: esEdicion
                    ? "Se actualizará la información del permiso."
                    : "Se registrará el nuevo permiso en el sistema.",

                icon: "question",

                showCancelButton: true,

                confirmButtonColor: esEdicion
                    ? "#dc3545"
                    : "#198754",

                cancelButtonColor: "#6c757d",

                confirmButtonText: esEdicion
                    ? "Sí, guardar cambios"
                    : "Sí, crear permiso",

                cancelButtonText: "Cancelar",

                reverseButtons: true,
            });

            if (resultado.isConfirmed) {
                formulario.submit();
            }
        }
    );

});