<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_rol',
        'nombres',
        'apellidos',
        'correo',
        'telefono',
        'usuario',
        'password',
        'activo',
        'debe_cambiar_password',
        'fecha_ultimo_cambio_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'debe_cambiar_password' => 'boolean',
        'fecha_ultimo_cambio_password' => 'datetime',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(
            Rol::class,
            'id_rol',
            'id_rol'
        );
    }

    public function tienePermiso(string $codigo): bool
    {
        return $this->rol()
            ->whereHas('permisos', function ($query) use ($codigo) {
                $query->where('codigo', $codigo)
                    ->where('activo', true);
            })
            ->exists();
    }
}