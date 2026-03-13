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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('project_manager_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('budget', 10, 2)->default(0);

            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();

            $table->enum('status', [
                'pending',
                'active',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
