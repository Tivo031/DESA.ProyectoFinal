document.addEventListener("DOMContentLoaded", () => {

    const formulario = document.getElementById("formUsuario");

    if (!formulario) {
        return;
    }

    const modo = formulario.dataset.modo;

    const campos = {
        nombres: document.getElementById("nombres"),
        apellidos: document.getElementById("apellidos"),
        correo: document.getElementById("correo"),
        telefono: document.getElementById("telefono"),
        usuario: document.getElementById("usuario"),
        id_rol: document.getElementById("id_rol"),
        password: document.getElementById("password"),
        password_confirmation: document.getElementById(
            "password_confirmation"
        ),
    };


    // EXPRESIONES REGULARES

    const regexNombre = /^[\p{L}\s'-]+$/u;

    const regexTelefono = /^\d{4}-\d{4}$/;

    const regexUsuario = /^[A-Za-z0-9._]+$/;

    const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


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


    // VALIDACIÓN DE CONTRASEÑA

    const validarPassword = (password) => {

        if (password.length < 10) {
            return "La contraseña debe tener al menos 10 caracteres.";
        }

        if (!/[a-z]/.test(password)) {
            return "La contraseña debe incluir al menos una letra minúscula.";
        }

        if (!/[A-Z]/.test(password)) {
            return "La contraseña debe incluir al menos una letra mayúscula.";
        }

        if (!/\d/.test(password)) {
            return "La contraseña debe incluir al menos un número.";
        }

        if (!/[^A-Za-z0-9]/.test(password)) {
            return "La contraseña debe incluir al menos un símbolo.";
        }

        return null;
    };


    // VALIDACIÓN DEL FORMULARIO

    const validarFormulario = () => {

        let valido = true;


        // LIMPIAR ERRORES

        Object.values(campos).forEach((campo) => {

            if (campo) {
                limpiarError(campo);
            }

        });


        // NOMBRES

        const nombres = campos.nombres.value.trim();

        if (nombres === "") {

            mostrarError(
                campos.nombres,
                "Los nombres son obligatorios."
            );

            valido = false;

        } else if (!regexNombre.test(nombres)) {

            mostrarError(
                campos.nombres,
                "Los nombres únicamente pueden contener letras, espacios, guiones y apóstrofes."
            );

            valido = false;
        }


        // APELLIDOS

        const apellidos = campos.apellidos.value.trim();

        if (apellidos === "") {

            mostrarError(
                campos.apellidos,
                "Los apellidos son obligatorios."
            );

            valido = false;

        } else if (!regexNombre.test(apellidos)) {

            mostrarError(
                campos.apellidos,
                "Los apellidos únicamente pueden contener letras, espacios, guiones y apóstrofes."
            );

            valido = false;
        }


        // CORREO

        const correo = campos.correo.value.trim();

        if (correo === "") {

            mostrarError(
                campos.correo,
                "El correo electrónico es obligatorio."
            );

            valido = false;

        } else if (!regexCorreo.test(correo)) {

            mostrarError(
                campos.correo,
                "Ingresa un correo electrónico válido."
            );

            valido = false;
        }


        // TELÉFONO

        const telefono = campos.telefono.value.trim();

        if (
            telefono !== "" &&
            !regexTelefono.test(telefono)
        ) {

            mostrarError(
                campos.telefono,
                "El teléfono debe tener el formato 5555-5555."
            );

            valido = false;
        }


        // USUARIO

        const usuario = campos.usuario.value.trim();

        if (usuario === "") {

            mostrarError(
                campos.usuario,
                "El nombre de usuario es obligatorio."
            );

            valido = false;

        } else if (usuario.length < 4) {

            mostrarError(
                campos.usuario,
                "El nombre de usuario debe tener al menos 4 caracteres."
            );

            valido = false;

        } else if (usuario.length > 50) {

            mostrarError(
                campos.usuario,
                "El nombre de usuario no puede superar los 50 caracteres."
            );

            valido = false;

        } else if (!regexUsuario.test(usuario)) {

            mostrarError(
                campos.usuario,
                "El usuario únicamente puede contener letras, números, puntos y guiones bajos."
            );

            valido = false;
        }


        // ROL

        if (campos.id_rol.value === "") {

            mostrarError(
                campos.id_rol,
                "Debes seleccionar un rol."
            );

            valido = false;
        }


        // CONTRASEÑA

        if (modo === "crear") {

            const password = campos.password.value;

            const confirmacion =
                campos.password_confirmation.value;


            if (password === "") {

                mostrarError(
                    campos.password,
                    "La contraseña es obligatoria."
                );

                valido = false;

            } else {

                const errorPassword =
                    validarPassword(password);

                if (errorPassword) {

                    mostrarError(
                        campos.password,
                        errorPassword
                    );

                    valido = false;
                }
            }


            if (confirmacion === "") {

                mostrarError(
                    campos.password_confirmation,
                    "Debes confirmar la contraseña."
                );

                valido = false;

            } else if (password !== confirmacion) {

                mostrarError(
                    campos.password_confirmation,
                    "Las contraseñas no coinciden."
                );

                valido = false;
            }
        }


        return valido;
    };


    // LIMPIAR ERROR AL MODIFICAR CAMPOS

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
                    : "¿Crear usuario?",

                text: esEdicion
                    ? "Se actualizará la información del usuario."
                    : "Se registrará el nuevo usuario en el sistema.",

                icon: "question",

                showCancelButton: true,

                confirmButtonColor: esEdicion
                    ? "#dc3545"
                    : "#198754",

                cancelButtonColor: "#6c757d",

                confirmButtonText: esEdicion
                    ? "Sí, guardar cambios"
                    : "Sí, crear usuario",

                cancelButtonText: "Cancelar",

                reverseButtons: true,

            });


            if (resultado.isConfirmed) {
                formulario.submit();
            }

        }
    );

});