<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
