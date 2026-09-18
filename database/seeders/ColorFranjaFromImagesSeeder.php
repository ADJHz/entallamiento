<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ColorFranjaFromImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('color_franja')->whereNotNull('imagen')->delete();

        $direcciones = DB::table('direcciones')->pluck('id');
        $tiposUniforme = DB::table('tipo_uniforme')->pluck('id', 'nombre');
        $timestamp = now();

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

                $datos = $this->parseNombreArchivo($archivo);

                if ($datos === null) {
                    continue;
                }

                [$color, $franja] = $datos;
                $imagen = '/camisas/'.$carpeta.'/'.rawurlencode($archivo);

                foreach ($direcciones as $direccionId) {
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
     * @return array{0: string, 1: string}|null
     */
    protected function parseNombreArchivo(string $archivo): ?array
    {
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

        if ($normalizado === '') {
            return null;
        }

        $partes = preg_split('/\s+/', trim($normalizado), -1, PREG_SPLIT_NO_EMPTY);

        if ($partes === false || count($partes) < 2) {
            $partes = [$normalizado, 'SIN FRANJA'];
        }

        $colorKey = $partes[0];
        $franjaKey = implode(' ', array_slice($partes, 1));

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

        $color = $colores[$colorKey] ?? null;
        $franja = $franjas[$franjaKey] ?? null;

        if ($color === null || $franja === null) {
            return null;
        }

        return [$color, $franja];
    }
}
