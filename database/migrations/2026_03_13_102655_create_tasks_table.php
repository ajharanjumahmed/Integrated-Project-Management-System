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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('milestone_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('assigned_to')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('priority', [
                'low',
                'medium',
                'high'
            ])->default('medium');

            $table->enum('status', [
                'pending',
                'started',
                'completed'
            ])->default('pending');

            $table->date('deadline')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
