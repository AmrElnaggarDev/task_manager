<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{

    protected $fillable = ['project_id', 'user_id', 'type', 'description'];

    public function project () :BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user ():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function log ($project, $user,string $type, string $description)
    {
        return self::create([
            'project_id' => is_numeric($project) ? $project : $project->id,
            'user_id' => is_numeric($user) ? $user : $user->id,
            'type' => $type,
            'description' => $description,
        ]);
    }


    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'task_created'        => 'Task Created',
            'task_status_updated' => 'Status Updated',
            'member_added'        => 'Member Added',
            'task_deleted'        => 'Task Deleted',
            'member_removed'      => 'Member Removed',
            'comment_added'       => 'Comment Added',
            'comment_deleted'      => 'Comment Deleted',
            default               => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getTypeIcon(): string
    {
        return match ($this->type) {
            'task_created'        => 'bi-plus-circle',
            'task_status_updated' => 'bi-arrow-repeat',
            'task_deleted'        => 'bi-trash',
            'member_added'        => 'bi-person-plus',
            'member_removed'      => 'bi-person-dash',
            'comment_added'       => 'bi-chat-left-text',
            'comment_deleted'     => 'bi-chat-left-x',
            default               => 'bi-info-circle',
        };
    }

    public function getTypeBadgeClass(): string
    {
        return match ($this->type) {
            'task_created'        => 'bg-primary',
            'task_status_updated' => 'bg-warning text-dark',
            'task_deleted'        => 'bg-danger',
            'member_added'        => 'bg-success',
            'member_removed'      => 'bg-danger',
            'comment_added'       => 'bg-info text-dark',
            'comment_deleted'     => 'bg-secondary',
            default               => 'bg-dark',
        };
    }
}
