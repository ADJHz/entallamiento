<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('elementos', function (Blueprint $table) {
            $table->id();
            $table->string('csp')->nullable();
            $table->string('nombre')->nullable();
            $table->string('cuip')->nullable();
            $table->string('genero')->nullable();
            $table->string('tipo_uniforme')->nullable();
            $table->string('coordinacion_area')->nullable();
            $table->string('direccion')->nullable();
            $table->string('color')->nullable();
            $table->string('franja')->nullable();
            $table->timestamps();
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elementos');
    }
};
