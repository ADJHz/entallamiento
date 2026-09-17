<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
class Elemento extends Model {}
