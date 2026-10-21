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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'User', 'Staff'])->default('User')->change();
        });

        DB::table('users')->where('role', 'User')->update(['role' => 'Staff']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Staff'])->default('Staff')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'User', 'Staff'])->default('User')->change();
        });
    }
};
