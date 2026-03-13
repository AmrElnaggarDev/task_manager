<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{

    protected $fillable = [
        'project_id', 'created_by', 'assigned_to', 'title', 'description', 'status', 'priority', 'deadline',
    ];

    public function creator() :BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assigner () :BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function project () :BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function comments () :HasMany
    {
        return $this->hasMany(Comment::class, 'task_id');
    }
}
