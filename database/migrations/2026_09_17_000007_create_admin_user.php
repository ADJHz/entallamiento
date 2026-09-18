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
        DB::table('users')->updateOrInsert(
            ['email' => 'admin'],
            [
                'name' => 'Admin',
                'password' => '$2y$12$7TCDTUhZ68o4L4TTAWec7uTrX3WN2evfWpz3sjhco/xMkRAR0f9Xm',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('email', 'admin')->delete();
    }
};
