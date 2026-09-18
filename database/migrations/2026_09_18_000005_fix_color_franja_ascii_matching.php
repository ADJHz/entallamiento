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
        // El intento anterior comparaba cadenas acentuadas por bytes y fallaba por
        // diferencias de normalización Unicode; aquí se compara en ASCII sin acentos.
        DB::table('color_franja')->whereNotNull('imagen')->delete();

        $direcciones = DB::table('direcciones')->get(['id', 'nombre']);
        $tiposUniforme = DB::table('tipo_uniforme')->pluck('id', 'nombre');
        $timestamp = now();

        $normalizar = fn (string $texto): string => Str::upper(Str::ascii($texto));

        $buscarDireccionIds = function (?string $contiene) use ($direcciones, $normalizar): array {
            if ($contiene === null) {
                return $direcciones->pluck('id')->all();
            }

            $contiene = $normalizar($contiene);

            return $direcciones
                ->filter(fn ($direccion): bool => Str::contains($normalizar($direccion->nombre), $contiene))
                ->pluck('id')
                ->all();
        };

        $carpetas = [
            'Cercanas' => 'Cercano',
            'Montados' => 'Montado',
            'Rapidas' => 'Rapido',
        ];

        // Archivo => [color, franja, subcadena de direccion (ASCII) o null para todas.
        $archivos = [
            'Azul Sin Franja.png' => ['Azul', 'N/A', null],
            'Blanca Sin Franja.png' => ['Blanco', 'N/A', null],
            'Rapida Azul.png' => ['Azul', 'N/A', null],
            'Rapida Gris.png' => ['Gris', 'N/A', null],
            'Rapida Negra.png' => ['Negro', 'N/A', null],
            'Genero.png' => ['General', 'Genero', 'POLICIA DE GENERO'],
            'Paramedicos.png' => ['General', 'Paramedicos', 'SERVICIOS MEDICOS'],
            'Proximidad.png' => ['General', 'Proximidad', null],
            'Robo.png' => ['General', 'Robo', 'ROBO DE VEHICULOS'],
            'Transito.png' => ['General', 'Transito', 'TRANSITO'],
        ];

        foreach ($carpetas as $carpeta => $tipoUniformeNombre) {
            $tipoUniformeId = $tiposUniforme[$tipoUniformeNombre] ?? null;
            $rutaCarpeta = public_path('camisas/'.$carpeta);

            if (! $tipoUniformeId || ! is_dir($rutaCarpeta)) {
                continue;
            }

            foreach (scandir($rutaCarpeta) ?: [] as $archivo) {
                if (! isset($archivos[$archivo])) {
                    continue;
                }

                [$color, $franja, $contiene] = $archivos[$archivo];
                $imagen = '/camisas/'.$carpeta.'/'.rawurlencode($archivo);

                foreach ($buscarDireccionIds($contiene) as $direccionId) {
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
