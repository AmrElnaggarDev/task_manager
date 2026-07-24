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


    public static array $labels = [
        'task_created'             => 'Task Created',
        'task_status_updated'      => 'Status Updated',
        'task_deleted'             => 'Task Deleted',
        'member_added'             => 'Member Added',
        'member_removed'           => 'Member Removed',
        'comment_added'            => 'Comment Added',
        'comment_deleted'          => 'Comment Deleted',
        'attachment_uploaded'      => 'Attachment Uploaded',
        'attachment_deleted'        => 'Attachment Deleted',
        'checklist_item_created'   => 'Checklist Added',
        'checklist_item_completed' => 'Checklist Completed',
        'checklist_item_reopened'  => 'Checklist Reopened',
        'checklist_item_deleted'   => 'Checklist Deleted',
    ];

    /**
     * Activity Type Icons (Bootstrap Icons)
     */
    public static array $icons = [
        'task_created'             => 'bi-plus-circle',
        'task_status_updated'      => 'bi-arrow-repeat',
        'task_deleted'             => 'bi-trash',
        'member_added'             => 'bi-person-plus',
        'member_removed'           => 'bi-person-minus',
        'comment_added'            => 'bi-chat-left-text',
        'comment_deleted'          => 'bi-chat-left-quote',
        'attachment_uploaded'      => 'bi-paperclip',
        'attachment_deleted'        => 'bi-file-earmark-x',
        'checklist_item_created'   => 'bi-list-check',
        'checklist_item_completed' => 'bi-check-circle',
        'checklist_item_reopened'  => 'bi-arrow-counterclockwise',
        'checklist_item_deleted'   => 'bi-trash',
    ];

    /**
     * Activity Type Badge Classes
     */
    public static array $badges = [
        'task_created'             => 'bg-primary',
        'task_status_updated'      => 'bg-info',
        'task_deleted'             => 'bg-danger',
        'member_added'             => 'bg-success',
        'member_removed'           => 'bg-secondary',
        'comment_added'            => 'bg-primary',
        'comment_deleted'          => 'bg-danger',
        'attachment_uploaded'      => 'bg-info',
        'attachment_deleted'        => 'bg-danger',
        'checklist_item_created'   => 'bg-primary',
        'checklist_item_completed' => 'bg-success',
        'checklist_item_reopened'  => 'bg-warning text-dark',
        'checklist_item_deleted'   => 'bg-danger',
    ];

    /**
     * Helper Methods Accessors
     */
    public function getLabelAttribute(): string
    {
        return static::$labels[$this->type] ?? $this->type;
    }

    public function getIconAttribute(): string
    {
        return static::$icons[$this->type] ?? 'bi-activity';
    }

    public function getBadgeClassAttribute(): string
    {
        return static::$badges[$this->type] ?? 'bg-secondary';
    }
}
