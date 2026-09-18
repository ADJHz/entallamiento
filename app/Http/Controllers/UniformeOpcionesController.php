<?php

namespace App\Http\Controllers;

use App\Models\ColorFranja;
use App\Models\Elemento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UniformeOpcionesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $elemento = Elemento::autenticado($request);

        abort_unless($elemento !== null, 403);

        $direccionId = $this->decryptId($request->input('direccion_id'));

        abort_unless($direccionId !== null, 422, 'La dirección no es válida.');

        $request->merge(['direccion_id' => $direccionId]);
        $data = $request->validate([
            'direccion_id' => ['required', 'integer', 'exists:direcciones,id'],
        ]);

        $opciones = ColorFranja::query()
            ->where('direccion_id', $data['direccion_id'])
            ->orderBy('color')
            ->orderBy('franja')
            ->get(['id', 'color', 'franja', 'descripcion', 'imagen']);

        return response()->json([
            'tipo_uniforme' => null,
            'options' => $opciones->map(fn (ColorFranja $opcion): array => [
                'id' => Crypt::encryptString((string) $opcion->id),
                'color' => $opcion->color,
                'franja' => $opcion->franja,
                'descripcion' => $opcion->descripcion,
                'imagen' => $opcion->imagen,
            ]),
        ]);
    }

    private function decryptId(mixed $value): ?int
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            $id = Crypt::decryptString($value);
        } catch (DecryptException) {
            return null;
        }

        return ctype_digit($id) && (int) $id > 0 ? (int) $id : null;
    }
}
