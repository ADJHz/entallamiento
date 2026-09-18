<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['direccion_id', 'tipo_uniforme_id', 'color', 'franja', 'imagen'])]
class ColorFranja extends Model
{
    protected $table = 'color_franja';

    /**
     * @return BelongsTo<Direccion, $this>
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }

    /**
     * @return BelongsTo<TipoUniforme, $this>
     */
    public function tipoUniforme(): BelongsTo
    {
        return $this->belongsTo(TipoUniforme::class);
    }
}
