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
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('asset')->nullable();
            $table->date('due_date')->nullable();
            $table->text('resolution')->nullable();
            $table->text('root_cause')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'department',
                'asset',
                'due_date',
                'resolution',
                'root_cause',
                'attachment_path',
                'attachment_name',
            ]);
        });
    }
};
