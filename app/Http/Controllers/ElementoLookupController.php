<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElementoLookupController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'csp' => ['required', 'string', 'max:255'],
            'cuip' => ['required', 'string', 'max:255'],
        ]);

        $elemento = Elemento::query()
            ->where('csp', trim($credentials['csp']))
            ->where('cuip', trim($credentials['cuip']))
            ->first(['id', 'nombre']);

        if (! $elemento) {
            return response()->json([
                'message' => 'No se encontró un elemento con esos datos.',
            ], 422);
        }

        return response()->json(['name' => $elemento->nombre]);
    }
}
