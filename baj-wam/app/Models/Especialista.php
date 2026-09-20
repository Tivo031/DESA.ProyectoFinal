<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialista extends Model
{
    protected $table = 'especialistas';

    protected $primaryKey = 'id_especialista';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'profesion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function citas()
    {
        return $this->hasMany(
            Cita::class,
            'id_especialista',
            'id_especialista'
        );
    }
}