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
        Schema::table('color_franja', function (Blueprint $table) {
            if (! Schema::hasColumn('color_franja', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('franja');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('color_franja', function (Blueprint $table) {
            if (Schema::hasColumn('color_franja', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });
    }
};
