document.addEventListener("DOMContentLoaded", () => {

    const formulario = document.getElementById("formRol");

    if (!formulario) {
        return;
    }

    const modo = formulario.dataset.modo;

    const campos = {
        nombre: document.getElementById("nombre"),
        descripcion: document.getElementById("descripcion"),
    };

    const botonTodos = document.getElementById(
        "seleccionarTodosPermisos"
    );

    const permisos = document.querySelectorAll(
        ".permiso-checkbox"
    );


    // EXPRESIONES REGULARES

    const regexNombre = /^[\p{L}0-9 _-]+$/u;


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


        // NOMBRE

        const nombre = campos.nombre.value.trim();

        if (nombre === "") {

            mostrarError(
                campos.nombre,
                "El nombre del rol es obligatorio."
            );

            valido = false;

        } else if (nombre.length < 3) {

            mostrarError(
                campos.nombre,
                "El nombre del rol debe tener al menos 3 caracteres."
            );

            valido = false;

        } else if (nombre.length > 50) {

            mostrarError(
                campos.nombre,
                "El nombre del rol no puede superar los 50 caracteres."
            );

            valido = false;

        } else if (!regexNombre.test(nombre)) {

            mostrarError(
                campos.nombre,
                "El nombre del rol únicamente puede contener letras, números, espacios, guiones y guiones bajos."
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

        } else if (descripcion.length > 150) {

            mostrarError(
                campos.descripcion,
                "La descripción no puede superar los 150 caracteres."
            );

            valido = false;
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


    // SELECCIONAR TODOS LOS PERMISOS

    if (botonTodos && permisos.length > 0) {

        const actualizarBoton = () => {

            const todosSeleccionados =
                Array.from(permisos).every(
                    (permiso) => permiso.checked
                );

            botonTodos.innerHTML = todosSeleccionados
                ? '<i class="bi bi-square me-1"></i>Quitar todos'
                : '<i class="bi bi-check2-square me-1"></i>Seleccionar todos';
        };


        botonTodos.addEventListener("click", () => {

            const todosSeleccionados =
                Array.from(permisos).every(
                    (permiso) => permiso.checked
                );

            permisos.forEach((permiso) => {
                permiso.checked = !todosSeleccionados;
            });

            actualizarBoton();
        });


        permisos.forEach((permiso) => {

            permiso.addEventListener(
                "change",
                actualizarBoton
            );

        });


        actualizarBoton();
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

            const esEdicion =
                modo === "editar";

            const resultado = await Swal.fire({

                title: esEdicion
                    ? "¿Guardar cambios?"
                    : "¿Crear rol?",

                text: esEdicion
                    ? "Se actualizará la información del rol."
                    : "Se registrará el nuevo rol en el sistema.",

                icon: "question",

                showCancelButton: true,

                confirmButtonColor: esEdicion
                    ? "#dc3545"
                    : "#198754",

                cancelButtonColor: "#6c757d",

                confirmButtonText: esEdicion
                    ? "Sí, guardar cambios"
                    : "Sí, crear rol",

                cancelButtonText: "Cancelar",

                reverseButtons: true,

            });

            if (resultado.isConfirmed) {
                formulario.submit();
            }

        }
    );

});