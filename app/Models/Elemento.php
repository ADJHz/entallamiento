<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
])]
class Elemento extends Authenticatable
{
    public function getAuthIdentifier(): string
    {
        return 'elemento:'.$this->getKey();
    }
}
