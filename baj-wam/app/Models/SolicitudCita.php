<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudCita extends Model
{
    protected $table = 'solicitudes_cita';

    protected $primaryKey = 'id_solicitud';

    const CREATED_AT = 'fecha_solicitud';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_servicio',
        'id_cita',
        'nombre',
        'telefono',
        'fecha',
        'hora',
        'comentario',
        'estado',
        'fecha_respuesta',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
    ];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(
            Servicio::class,
            'id_servicio',
            'id_servicio'
        );
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(
            Cita::class,
            'id_cita',
            'id_cita'
        );
    }
}