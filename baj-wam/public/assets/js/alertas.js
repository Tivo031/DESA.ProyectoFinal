document.addEventListener("DOMContentLoaded", () => {
    const alerta = document.getElementById("alertaSistema");

    if (!alerta) {
        return;
    }

    const tipo = alerta.dataset.tipo;
    const mensaje = alerta.dataset.mensaje;

    let colorConfirmacion = "#6f42c1";

    if (tipo === "success") {
        colorConfirmacion = "#198754";
    }

    if (tipo === "error") {
        colorConfirmacion = "#dc3545";
    }

    Swal.fire({
        icon: tipo,
        title:
            tipo === "success"
                ? "Operación realizada"
                : "Ocurrió un problema",

        text: mensaje,

        confirmButtonColor: colorConfirmacion,

        confirmButtonText: "Aceptar",
    });
});