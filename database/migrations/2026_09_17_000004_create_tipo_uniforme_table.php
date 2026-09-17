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
        Schema::create('tipo_uniforme', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
        });

        $timestamp = now();

        DB::table('tipo_uniforme')->insert([
            ['nombre' => 'Cercano', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['nombre' => 'Rapido', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['nombre' => 'Montado', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_uniforme');
    }
};
