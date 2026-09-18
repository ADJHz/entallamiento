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
        // La migración anterior comparó nombres con igualdad exacta y fallo por acentos;
        // aquí se relimpia y resiembra usando coincidencia por subcadena (case/accent-insensitive).
        DB::table('color_franja')->whereNotNull('imagen')->delete();

        $direcciones = DB::table('direcciones')->get(['id', 'nombre']);
        $tiposUniforme = DB::table('tipo_uniforme')->pluck('id', 'nombre');
        $timestamp = now();

        $buscarDireccionIds = function (?string $contiene) use ($direcciones): array {
            if ($contiene === null) {
                return $direcciones->pluck('id')->all();
            }

            return $direcciones
                ->filter(fn ($direccion): bool => Str::contains(
                    Str::upper($direccion->nombre),
                    Str::upper($contiene),
                ))
                ->pluck('id')
                ->all();
        };

        $carpetas = [
            'Cercanas' => 'Cercano',
            'Montados' => 'Montado',
            'Rapidas' => 'Rapido',
        ];

        // Archivo => [color, franja, subcadena de direccion o null para todas las direcciones].
        $archivos = [
            'Azul Sin Franja.png' => ['Azul', 'N/A', null],
            'Blanca Sin Franja.png' => ['Blanco', 'N/A', null],
            'Rapida Azul.png' => ['Azul', 'N/A', null],
            'Rapida Gris.png' => ['Gris', 'N/A', null],
            'Rapida Negra.png' => ['Negro', 'N/A', null],
            'Genero.png' => ['General', 'Genero', 'POLICÍA DE GÉNERO'],
            'Paramedicos.png' => ['General', 'Paramedicos', 'SERVICIOS MEDICOS'],
            'Proximidad.png' => ['General', 'Proximidad', null],
            'Robo.png' => ['General', 'Robo', 'ROBO DE VEHÍCULOS'],
            'Transito.png' => ['General', 'Transito', 'TRÁNSITO'],
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
