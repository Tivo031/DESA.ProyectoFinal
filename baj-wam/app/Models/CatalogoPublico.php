<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogoPublico extends Model
{
    protected $table = 'catalogo_publico';
    protected $primaryKey = 'id_producto';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'visible',
        'orden_visualizacion',
        'fecha_publicacion',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'fecha_publicacion' => 'datetime',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(
            Producto::class,
            'id_producto',
            'id_producto'
        );
    }
}