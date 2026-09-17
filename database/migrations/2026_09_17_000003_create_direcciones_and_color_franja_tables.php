<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
        });

        Schema::create('color_franja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direccion_id')->constrained('direcciones')->cascadeOnDelete();
            $table->string('color');
            $table->string('franja');
            $table->timestamps();
            $table->unique(['direccion_id', 'color', 'franja']);
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
        });

        $direcciones = [
            'COORDINACIÓN DE GRUPOS TÁCTICOS',
            'DIRECCIÓN GENERAL DE POLICÍA DE GÉNERO',
            'DIRECCIÓN GENERAL DE PREVENCIÓN Y REINSERCIÓN SOCIAL',
            'DIRECCIÓN GENERAL DE SEGURIDAD PÚBLICA Y TRÁNSITO',
            'DIRECCIÓN GENERAL DE COMBATE AL ROBO DE VEHÍCULOS Y TRANSPORTE',
            'SUBSECRETARÍA DE POLICÍA ESTATAL',
        ];

        $timestamp = now();

        foreach ($direcciones as $nombre) {
            DB::table('direcciones')->insert([
                'nombre' => $nombre,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_franja');
        Schema::dropIfExists('direcciones');
    }
};
