<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $direcciones = DB::table('direcciones')->pluck('id', 'nombre');
        $tiposUniforme = DB::table('tipo_uniforme')->pluck('id', 'nombre');
        $timestamp = now();

        // Carpeta => tipo_uniforme.nombre.
        $carpetas = [
            'Cercanas' => 'Cercano',
            'Montados' => 'Montado',
            'Rapidas' => 'Rapido',
        ];

        // Archivo => [color, franja, direccion o null para todas las direcciones].
        $archivos = [
            'Azul Sin Franja.png' => ['Azul', 'N/A', null],
            'Blanca Sin Franja.png' => ['Blanco', 'N/A', null],
            'Rapida Azul.png' => ['Azul', 'N/A', null],
            'Rapida Gris.png' => ['Gris', 'N/A', null],
            'Rapida Negra.png' => ['Negro', 'N/A', null],
            'Genero.png' => ['General', 'Genero', 'DIRECCIÓN GENERAL DE POLICÍA DE GÉNERO'],
            'Paramedicos.png' => ['General', 'Paramedicos', null],
            'Proximidad.png' => ['General', 'Proximidad', null],
            'Robo.png' => ['General', 'Robo', 'DIRECCIÓN GENERAL DE COMBATE AL ROBO DE VEHÍCULOS Y TRANSPORTE'],
            'Transito.png' => ['General', 'Transito', 'DIRECCIÓN GENERAL DE SEGURIDAD PÚBLICA Y TRÁNSITO'],
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

                [$color, $franja, $direccionNombre] = $archivos[$archivo];
                $imagen = '/camisas/'.$carpeta.'/'.rawurlencode($archivo);

                $direccionIds = $direccionNombre
                    ? array_filter([$direcciones[$direccionNombre] ?? null])
                    : $direcciones->all();

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
