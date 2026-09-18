<?php

namespace App\Auth;

use App\Models\Elemento;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class CombinedUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        $identifier = (string) $identifier;

        if (str_starts_with($identifier, 'elemento:')) {
            $elementoId = substr($identifier, strlen('elemento:'));

            if (! ctype_digit($elementoId)) {
                return null;
            }

            return Elemento::query()->find((int) $elementoId);
        }

        return parent::retrieveById($identifier);
    }
}
