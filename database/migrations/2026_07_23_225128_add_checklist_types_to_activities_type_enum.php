<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('type', [
                'task_created',
                'task_status_updated',
                'task_deleted',
                'member_added',
                'member_removed',
                'comment_added',
                'comment_deleted',
                'attachment_uploaded',
                'attachment_deleted',

                'checklist_item_created',
                'checklist_item_completed',
                'checklist_item_reopened',
                'checklist_item_deleted',
            ])->default('task_created')->change();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('type', [
                'task_created',
                'task_status_updated',
                'task_deleted',
                'member_added',
                'member_removed',
                'comment_added',
                'comment_deleted',
                'attachment_uploaded',
                'attachment_deleted',
            ])->default('task_created')->change();
        });
    }
};
