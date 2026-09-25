<?php

use App\Models\Task;
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
            $table->enum('priority', [Task::PRIORITY_HIGH, Task::PRIORITY_MEDIUM, Task::PRIORITY_LOW])->default(Task::PRIORITY_HIGH);
            $table->timestamp('remind_at')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
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
