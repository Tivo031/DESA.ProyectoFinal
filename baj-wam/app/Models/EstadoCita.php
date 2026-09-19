<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoCita extends Model
{
    protected $table = 'estados_cita';

    protected $primaryKey = 'id_estado_cita';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(
            Cita::class,
            'id_estado_cita',
            'id_estado_cita'
        );
    }
}