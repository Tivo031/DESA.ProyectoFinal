document.addEventListener('DOMContentLoaded', function () {
    const servicio = document.getElementById('id_servicio');
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const horarioCalculado = document.getElementById('horarioCalculado');
    const duracionServicio = document.getElementById('duracionServicio');

    if (!servicio || !horaInicio || !horaFin) {
        return;
    }

    function calcularHorario() {
        const opcion = servicio.options[servicio.selectedIndex];

        const duracion = parseInt(
            opcion?.dataset?.duracion || 0
        );

        const inicio = horaInicio.value;

        if (!inicio || !duracion) {
            horaFin.value = '';

            horarioCalculado.textContent =
                'Selecciona un servicio y una hora';

            duracionServicio.textContent = '';

            return;
        }

        const partes = inicio.split(':');

        const fecha = new Date();

        fecha.setHours(
            parseInt(partes[0]),
            parseInt(partes[1]),
            0,
            0
        );

        fecha.setMinutes(
            fecha.getMinutes() + duracion
        );

        const horas = String(
            fecha.getHours()
        ).padStart(2, '0');

        const minutos = String(
            fecha.getMinutes()
        ).padStart(2, '0');

        const fin = `${horas}:${minutos}`;

        horaFin.value = fin;

        horarioCalculado.textContent =
            `${inicio} - ${fin}`;

        duracionServicio.textContent =
            `(${duracion} minutos)`;
    }

    servicio.addEventListener(
        'change',
        calcularHorario
    );

    horaInicio.addEventListener(
        'change',
        calcularHorario
    );

    calcularHorario();
});