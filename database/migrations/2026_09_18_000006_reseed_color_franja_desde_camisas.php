<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Las imagenes en public/camisas ahora siguen el catalogo original de
        // colores y franjas; se relimpia lo sembrado antes con datos obsoletos.
        DB::table('color_franja')->whereNotNull('imagen')->delete();

        $direccionIds = DB::table('direcciones')->pluck('id')->all();
        $tiposUniforme = DB::table('tipo_uniforme')->pluck('id', 'nombre');
        $timestamp = now();

        $parseNombreArchivo = function (string $archivo) : ?array {
            $nombreBase = Str::beforeLast($archivo, '.png');
            $normalizado = Str::of($nombreBase)
                ->trim()
                ->squish()
                ->replace(['-', '/'], ' ')
                ->upper()
                ->toString();

            if (Str::startsWith($normalizado, 'RAPIDA ')) {
                $normalizado = Str::after($normalizado, 'RAPIDA ');
            }

            $partes = preg_split('/\s+/', trim($normalizado), -1, PREG_SPLIT_NO_EMPTY);

            if ($partes === false || count($partes) < 2) {
                return null;
            }

            $colores = [
                'AZUL' => 'Azul',
                'BLANCA' => 'Blanco',
                'BLANCO' => 'Blanco',
                'GRIS' => 'Gris',
                'NEGRA' => 'Negro',
                'NEGRO' => 'Negro',
            ];

            $franjas = [
                'SIN FRANJA' => 'N/A',
                'FRANJA ROSA' => 'Rosa',
                'ROSA' => 'Rosa',
                'VERDE' => 'Verde',
                'AMARILLO' => 'Amarillo',
                'NARANJA' => 'Naranja',
                'AZUL ROJO' => 'Azul/Rojo',
                'AZUL ROJA' => 'Azul/Rojo',
            ];

            $color = $colores[$partes[0]] ?? null;
            $franja = $franjas[implode(' ', array_slice($partes, 1))] ?? null;

            return $color && $franja ? [$color, $franja] : null;
        };

        $carpetas = [
            'Cercanas' => 'Cercano',
            'Montados' => 'Montado',
            'Rapidas' => 'Rapido',
        ];

        foreach ($carpetas as $carpeta => $tipoUniformeNombre) {
            $tipoUniformeId = $tiposUniforme[$tipoUniformeNombre] ?? null;
            $rutaCarpeta = public_path('camisas/'.$carpeta);

            if (! $tipoUniformeId || ! is_dir($rutaCarpeta)) {
                continue;
            }

            foreach (scandir($rutaCarpeta) ?: [] as $archivo) {
                if (! is_file($rutaCarpeta.DIRECTORY_SEPARATOR.$archivo) || ! Str::endsWith(Str::lower($archivo), '.png')) {
                    continue;
                }

                $parsed = $parseNombreArchivo($archivo);

                if (! $parsed) {
                    continue;
                }

                [$color, $franja] = $parsed;
                $imagen = '/camisas/'.$carpeta.'/'.rawurlencode($archivo);

                foreach ($direccionIds as $direccionId) {
                    DB::table('color_franja')->updateOrInsert(
                        [
                            'direccion_id' => $direccionId,
                            'tipo_uniforme_id' => $tipoUniformeId,
                            'color' => $color,
                            'franja' => $franja,
                        ],
                        [
                            'imagen' => $imagen,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ],
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('color_franja')->whereNotNull('imagen')->delete();
    }
};
