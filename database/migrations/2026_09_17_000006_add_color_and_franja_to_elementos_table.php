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
        if (! Schema::hasColumn('elementos', 'color')) {
            Schema::table('elementos', function (Blueprint $table) {
                $table->string('color')->nullable()->after('direccion');
            });
        }

        if (! Schema::hasColumn('elementos', 'franja')) {
            Schema::table('elementos', function (Blueprint $table) {
                $table->string('franja')->nullable()->after('color');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elementos', function (Blueprint $table) {
            $table->dropColumn(['color', 'franja']);
        });
    }
};
