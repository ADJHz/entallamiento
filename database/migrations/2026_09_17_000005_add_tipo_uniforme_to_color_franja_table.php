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
        if (! Schema::hasColumn('color_franja', 'tipo_uniforme_id')) {
            Schema::table('color_franja', function (Blueprint $table) {
                $table->foreignId('tipo_uniforme_id')
                    ->nullable()
                    ->after('direccion_id')
                    ->constrained('tipo_uniforme')
                    ->nullOnDelete();
            });
        }

        // The old unique index is currently used by the direccion foreign key.
        Schema::table('color_franja', function (Blueprint $table) {
            $table->index('direccion_id', 'color_franja_direccion_id_index');
        });

        Schema::table('color_franja', function (Blueprint $table) {
            $table->dropUnique('color_franja_direccion_id_color_franja_unique');
        });

        Schema::table('color_franja', function (Blueprint $table) {
            $table->unique(['direccion_id', 'tipo_uniforme_id', 'color', 'franja']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('color_franja', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_uniforme_id');
        });

        Schema::table('color_franja', function (Blueprint $table) {
            $table->dropUnique([
                'direccion_id',
                'tipo_uniforme_id',
                'color',
                'franja',
            ]);
            $table->unique(['direccion_id', 'color', 'franja']);
        });
    }
};
