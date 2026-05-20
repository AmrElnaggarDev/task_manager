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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', [
                'task_created', 'task_status_updated', 'task_deleted', 'member_added', 'member_removed', 'comment_added',
                'comment_deleted'
            ])->default('task_created');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
