<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskChecklistItem extends Model
{
//   protected $guarded = [];
    protected $fillable = [
        'task_id',
        'created_by',
        'title',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function task () : BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function creator () : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')
            ->withDefault([
                'name' => 'Unknown User',
            ]);
    }
}
