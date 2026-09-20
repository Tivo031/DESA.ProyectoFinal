<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';

    protected $primaryKey = 'id_cita';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_paciente',
        'id_especialista',
        'id_servicio',
        'id_estado_cita',
        'id_usuario_registro',
        'inicio',
        'fin',
        'observaciones',
        'motivo_cancelacion',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(
            Paciente::class,
            'id_paciente',
            'id_paciente'
        );
    }

    public function especialista()
    {
        return $this->belongsTo(
            Especialista::class,
            'id_especialista',
            'id_especialista'
        );
    }

    public function servicio()
    {
        return $this->belongsTo(
            Servicio::class,
            'id_servicio',
            'id_servicio'
        );
    }

    public function estado()
    {
        return $this->belongsTo(
            EstadoCita::class,
            'id_estado_cita',
            'id_estado_cita'
        );
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(
            Usuario::class,
            'id_usuario_registro',
            'id_usuario'
        );
    }
}