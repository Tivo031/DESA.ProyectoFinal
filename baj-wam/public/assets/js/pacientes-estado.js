(() => {
    if (window.bajwamPacientesEstado) return;
    window.bajwamPacientesEstado = true;

    document.addEventListener("submit", async (evento) => {
        const formulario = evento.target;
        if (!(formulario instanceof HTMLFormElement) || !formulario.matches("[data-paciente-estado]")) return;

        evento.preventDefault();
        if (formulario.dataset.confirmando === "1") return;
        formulario.dataset.confirmando = "1";

        const activar = formulario.elements.namedItem("activo").value === "1";
        const accion = activar ? "activar" : "desactivar";
        const nombre = formulario.dataset.pacienteNombre || "este paciente";
        const mensaje = `¿Deseas ${accion} a ${nombre}? Se conservaran sus citas y su historial clinico.`;

        try {
            const confirmado = typeof window.Swal?.fire === "function"
                ? (await window.Swal.fire({
                    title: activar ? "¿Activar paciente?" : "¿Desactivar paciente?",
                    text: mensaje,
                    icon: activar ? "question" : "warning",
                    showCancelButton: true,
                    confirmButtonColor: activar ? "#198754" : "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: activar ? "Si, activar" : "Si, desactivar",
                    cancelButtonText: "Cancelar",
                    reverseButtons: true,
                })).isConfirmed
                : window.confirm(mensaje);

            if (!confirmado) return;
            formulario.querySelectorAll('button[type="submit"]').forEach((boton) => {
                boton.disabled = true;
            });
            HTMLFormElement.prototype.submit.call(formulario);
        } catch (error) {
            window.alert("No se pudo confirmar la accion. Recarga la pagina e intenta nuevamente.");
        } finally {
            delete formulario.dataset.confirmando;
        }
    });
})();
