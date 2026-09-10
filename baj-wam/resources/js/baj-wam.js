document.addEventListener('DOMContentLoaded', () => {
    // Confirmación simple para botones de eliminación.
    document.querySelectorAll('[data-confirm-delete]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const message = button.dataset.confirmDelete || '¿Deseas eliminar este registro?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // Oculta automáticamente los mensajes temporales después de 5 segundos.
    document.querySelectorAll('[data-auto-dismiss]').forEach((alert) => {
        window.setTimeout(() => {
            const instance = bootstrap.Alert.getOrCreateInstance(alert);
            instance.close();
        }, 5000);
    });
});
