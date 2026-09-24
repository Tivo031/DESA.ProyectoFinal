<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_categoria',
        'codigo',
        'nombre',
        'descripcion',
        'presentacion',
        'unidad_medida',
        'precio_referencia',
        'existencia_minima',
        'imagen_url',
        'activo',
    ];

    protected $casts = [
        'precio_referencia' => 'decimal:2',
        'existencia_minima' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            CategoriaProducto::class,
            'id_categoria',
            'id_categoria'
        );
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(
            MovimientoInventario::class,
            'id_producto',
            'id_producto'
        );
    }

    public function catalogo(): HasOne
    {
        return $this->hasOne(
            CatalogoPublico::class,
            'id_producto',
            'id_producto'
        );
    }
}