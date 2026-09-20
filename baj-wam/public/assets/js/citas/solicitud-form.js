document.addEventListener('DOMContentLoaded', function () {
    const existente = document.getElementById('pacienteExistente');
    const nuevo = document.getElementById('pacienteNuevo');

    const bloqueExistente = document.getElementById(
        'bloquePacienteExistente'
    );

    const bloqueNuevo = document.getElementById(
        'bloquePacienteNuevo'
    );

    function actualizar() {
        if (existente.checked) {
            bloqueExistente.style.display = 'block';
            bloqueNuevo.style.display = 'none';
        } else {
            bloqueExistente.style.display = 'none';
            bloqueNuevo.style.display = 'block';
        }
    }

    existente.addEventListener('change', actualizar);
    nuevo.addEventListener('change', actualizar);

    actualizar();
});