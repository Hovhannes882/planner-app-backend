<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('aspect_id')
                ->constrained('aspects')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->date('day');
            $table->enum('priority', ['high', 'medium', 'low'])->default('high');
            $table->timestamp('remind_at')->nullable();
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->softDeletes();
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
