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
            if (! Schema::hasColumn('elementos', 'direccion_id')) {
                $table->foreignId('direccion_id')->nullable()->after('direccion')
                    ->constrained('direcciones')->nullOnDelete();
            }

            if (! Schema::hasColumn('elementos', 'color_franja_id')) {
                $table->foreignId('color_franja_id')->nullable()->after('franja')
                    ->constrained('color_franja')->nullOnDelete();
            }

            if (! Schema::hasColumn('elementos', 'talla_chamarra')) {
                $table->string('talla_chamarra')->nullable();
            }

            if (! Schema::hasColumn('elementos', 'talla_camisa')) {
                $table->string('talla_camisa')->nullable();
            }

            if (! Schema::hasColumn('elementos', 'talla_pantalon')) {
                $table->string('talla_pantalon')->nullable();
            }

            if (! Schema::hasColumn('elementos', 'talla_cinturon')) {
                $table->string('talla_cinturon')->nullable();
            }

            if (! Schema::hasColumn('elementos', 'talla_botas')) {
                $table->string('talla_botas')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elementos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('direccion_id');
            $table->dropConstrainedForeignId('color_franja_id');
            $table->dropColumn([
                'talla_chamarra',
                'talla_camisa',
                'talla_pantalon',
                'talla_cinturon',
                'talla_botas',
            ]);
        });
    }
};
