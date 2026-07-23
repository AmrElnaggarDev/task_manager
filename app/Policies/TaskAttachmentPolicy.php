<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;

class TaskAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view/download the model.
     */
    public function view(User $user, TaskAttachment $taskAttachment): bool
    {
        $task = $taskAttachment->task;
        if (! $task) return false;

        $project = $task->project;
        if (! $project) return false;

        if ($user->id === $project->owner_id) {
            return true;
        }

        return $project->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Task $task): bool
    {
        $project = $task->project;

        if (! $project) {
            return false;
        }

        $canAccessProject = $user->id === $project->owner_id
            || $project->members()
                ->where('user_id', $user->id)
                ->exists();

        $canUpload = $user->id === $task->created_by
            || $user->id === $task->assigned_to
            || $user->id === $project->owner_id;

        return $canAccessProject && $canUpload;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskAttachment $taskAttachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
public function delete(User $user, TaskAttachment $taskAttachment): bool
{
    $task = $taskAttachment->task;
    if (! $task) return false;

    $project = $task->project;
    if (! $project) return false;

    $canAccessProject = $user->id === $project->owner_id
        || $project->members()->where('user_id', $user->id)->exists();

    $canDelete = $user->id === $taskAttachment->uploaded_by
        || $user->id === $task->created_by
        || $user->id === $project->owner_id;

    return $canAccessProject && $canDelete;
}

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskAttachment $taskAttachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskAttachment $taskAttachment): bool
    {
        return false;
    }
}
