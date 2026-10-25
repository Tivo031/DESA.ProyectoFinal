document.addEventListener('DOMContentLoaded', function () {
    const existente = document.getElementById('pacienteExistente');
    const nuevo = document.getElementById('pacienteNuevo');
    const bloqueExistente = document.getElementById('bloquePacienteExistente');
    const bloqueNuevo = document.getElementById('bloquePacienteNuevo');
    const buscarPaciente = document.getElementById('buscarPaciente');
    const selectorPaciente = document.getElementById('id_paciente');

    if (!existente || !nuevo || !bloqueExistente || !bloqueNuevo) {
        return;
    }

    function actualizarTipoPaciente() {
        const esExistente = existente.checked;
        bloqueExistente.style.display = esExistente ? 'block' : 'none';
        bloqueNuevo.style.display = esExistente ? 'none' : 'block';
    }

    existente.addEventListener('change', actualizarTipoPaciente);
    nuevo.addEventListener('change', actualizarTipoPaciente);
    actualizarTipoPaciente();

    if (!buscarPaciente || !selectorPaciente) {
        return;
    }

    let temporizador;

    buscarPaciente.addEventListener('input', function () {
        window.clearTimeout(temporizador);

        const termino = buscarPaciente.value.trim();

        if (termino.length > 0 && termino.length < 2 && !/^\d{13}$/.test(termino) && !/^PAC-\d{6,}$/i.test(termino)) {
            return;
        }

        temporizador = window.setTimeout(function () {
            const url = new URL(buscarPaciente.dataset.url, window.location.origin);

            if (termino) {
                url.searchParams.set('q', termino);
            }

            fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            })
                .then(function (respuesta) {
                    if (!respuesta.ok) {
                        throw new Error('No fue posible buscar pacientes.');
                    }

                    return respuesta.json();
                })
                .then(function (pacientes) {
                    selectorPaciente.innerHTML = '<option value="">Selecciona un paciente</option>';

                    pacientes.forEach(function (paciente) {
                        const opcion = document.createElement('option');
                        opcion.value = paciente.id_paciente;
                        opcion.dataset.activo = paciente.activo ? '1' : '0';
                        opcion.disabled = !paciente.activo;
                        opcion.textContent = paciente.nombre_completo + ' · ' + paciente.numero_expediente + (paciente.activo ? '' : ' · INACTIVO');
                        selectorPaciente.appendChild(opcion);
                    });
                })
                .catch(function () {
                    // El backend conserva la validación y autorización del flujo.
                });
        }, 300);
    });
});
