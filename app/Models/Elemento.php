<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

#[Fillable([
    'csp',
    'nombre',
    'cuip',
    'genero',
    'tipo_uniforme',
    'coordinacion_area',
    'direccion',
    'color',
    'franja',
    'direccion_id',
    'color_franja_id',
    'talla_chamarra',
    'talla_camisa',
    'talla_pantalon',
    'talla_cinturon',
    'talla_botas',
])]
class Elemento extends Authenticatable
{
    public function getAuthIdentifier(): string
    {
        return 'elemento:'.$this->getKey();
    }

    /**
     * Resuelve el elemento autenticado desde la sesión (ver AuthenticateActor).
     */
    public static function autenticado(Request $request): ?self
    {
        $elementoId = $request->session()->get('elemento_auth_id');

        return is_numeric($elementoId) ? self::query()->find((int) $elementoId) : null;
    }

    /**
     * @return BelongsTo<Direccion, $this>
     */
    public function direccionCatalogo(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'direccion_id');
    }

    /**
     * @return BelongsTo<ColorFranja, $this>
     */
    public function colorFranjaSeleccionado(): BelongsTo
    {
        return $this->belongsTo(ColorFranja::class, 'color_franja_id');
    }
}
