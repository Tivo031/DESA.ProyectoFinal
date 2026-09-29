<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Consulta extends Model
{
    protected $table = 'consultas';

    protected $primaryKey = 'id_consulta';

    public $timestamps = false;

    protected $fillable = [
        'id_cita',
        'id_usuario_registro',
        'fecha_consulta',
        'motivo_consulta',
        'observaciones',
        'diagnostico',
        'tratamiento_realizado',
        'recomendaciones',
    ];

    protected $casts = [
        'fecha_consulta' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    public function cita(): BelongsTo
    {
        return $this->belongsTo(
            Cita::class,
            'id_cita',
            'id_cita'
        );
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'id_usuario_registro',
            'id_usuario'
        );
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(
            Producto::class,
            'consulta_productos',
            'id_consulta',
            'id_producto'
        )->withPivot(
                'cantidad_recomendada',
                'indicaciones'
            );
    }
}