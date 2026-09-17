document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.getElementById("formLogin");

    if (!formulario) {
        return;
    }

    const usuario = document.getElementById("usuario");
    const password = document.getElementById("password");

    const mostrarError = (campo, mensaje) => {
        campo.classList.add("is-invalid");

        const contenedorError = document.getElementById(
            `error-${campo.id}`
        );

        if (contenedorError) {
            contenedorError.textContent = mensaje;
        }
    };

    const limpiarError = (campo) => {
        campo.classList.remove("is-invalid");

        const contenedorError = document.getElementById(
            `error-${campo.id}`
        );

        if (contenedorError) {
            contenedorError.textContent = "";
        }
    };

    usuario.addEventListener("input", () => {
        limpiarError(usuario);
    });

    password.addEventListener("input", () => {
        limpiarError(password);
    });

    formulario.addEventListener("submit", (evento) => {
        let valido = true;

        limpiarError(usuario);
        limpiarError(password);

        if (usuario.value.trim() === "") {
            mostrarError(
                usuario,
                "Ingresa tu usuario o correo electrónico."
            );

            valido = false;
        }

        if (password.value === "") {
            mostrarError(
                password,
                "Ingresa tu contraseña."
            );

            valido = false;
        }

        if (!valido) {
            evento.preventDefault();

            const primerError =
                formulario.querySelector(".is-invalid");

            if (primerError) {
                primerError.focus();
            }
        }
    });
});