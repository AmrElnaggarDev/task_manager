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
}
