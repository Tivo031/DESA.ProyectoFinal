document.addEventListener("DOMContentLoaded", () => {

    const boton = document.getElementById(
        "btnRestablecerPassword"
    );

    const resultadoPassword = document.getElementById(
        "resultadoPassword"
    );

    if (!boton || !resultadoPassword) {
        return;
    }

    boton.addEventListener("click", async () => {

        // CONFIRMACIÓN

        const resultado = await Swal.fire({
            title: "¿Restablecer contraseña?",
            text: `Se generará una contraseña temporal para ${boton.dataset.nombre}.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, restablecer",
            cancelButtonText: "Cancelar",
            reverseButtons: true,
        });

        if (!resultado.isConfirmed) {
            return;
        }

        try {

            boton.disabled = true;

            // RESTABLECER CONTRASEÑA

            const token = document.querySelector(
                'meta[name="csrf-token"]'
            );

            const respuesta = await fetch(
                boton.dataset.url,
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token.content,
                        "Accept": "application/json",
                    },
                }
            );

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                throw new Error(
                    datos.message ||
                    "No fue posible restablecer la contraseña."
                );
            }

            // MOSTRAR CONTRASEÑA TEMPORAL

            resultadoPassword.innerHTML = `
                <div class="alert alert-success mt-3" role="alert">

                    <div class="fw-bold mb-2">
                        ${datos.message}
                    </div>

                    <div>
                        Contraseña temporal:
                        <strong>
                            ${datos.password_temporal}
                        </strong>
                    </div>

                    <div class="small mt-2">
                        Copia esta contraseña ahora.
                        No volverá a mostrarse.
                    </div>

                </div>
            `;

        } catch (error) {

            resultadoPassword.innerHTML = `
                <div class="alert alert-danger mt-3" role="alert">
                    ${error.message}
                </div>
            `;

        } finally {

            boton.disabled = false;

        }

    });

});