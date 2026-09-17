<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre'])]
class TipoUniforme extends Model
{
    protected $table = 'tipo_uniforme';

    /**
     * @return HasMany<ColorFranja, $this>
     */
    public function colorFranjas(): HasMany
    {
        return $this->hasMany(ColorFranja::class);
    }
}
