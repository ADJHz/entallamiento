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
        Schema::table('elementos', function (Blueprint $table) {
            $table->string('csp')->nullable()->change();
            $table->string('nombre')->nullable()->change();
            $table->string('cuip')->nullable()->change();
            $table->string('genero')->nullable()->change();
            $table->string('tipo_uniforme')->nullable()->change();
            $table->string('coordinacion_area')->nullable()->change();
            $table->string('direccion')->nullable()->change();
        });

        if (! Schema::hasIndex('elementos', 'elementos_csp_unique')) {
            Schema::table('elementos', function (Blueprint $table) {
                $table->unique('csp', 'elementos_csp_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elementos', function (Blueprint $table) {
            if (Schema::hasIndex('elementos', 'elementos_csp_unique')) {
                $table->dropUnique('elementos_csp_unique');
            }
            $table->string('csp')->nullable(false)->change();
            $table->string('nombre')->nullable(false)->change();
            $table->string('cuip')->nullable(false)->change();
            $table->string('genero')->nullable(false)->change();
            $table->string('tipo_uniforme')->nullable(false)->change();
            $table->string('coordinacion_area')->nullable(false)->change();
            $table->string('direccion')->nullable(false)->change();
        });
    }
};
