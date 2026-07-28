<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id
            || $project->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task, Project $project): bool
    {
        return $user->id === $project->owner_id
            || $project->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id
            || $project->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        $project = $task->project;
        if (! $project) {
            return false;
        }

        $canAccessProject = $user->id === $project->owner_id
            || $project->members()->where('user_id', $user->id)->exists();

        return $canAccessProject && $user->id === $task->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        $project = $task->project;
        if (! $project) {
            return false;
        }

        $canAccessProject = $user->id === $project->owner_id
            || $project->members()->where('user_id', $user->id)->exists();

        return $canAccessProject && $user->id === $task->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }

    public function updateStatus(User $user, Task $task): bool
    {
        $project = $task->project;
        $canAccessProject = $user->id === $project->owner_id
            || $project->members()->where('user_id', $user->id)->exists();
        $canUpdateStatus = $user->id === $task->created_by
            || $user->id === $task->assigned_to;

        return $canAccessProject && $canUpdateStatus;
    }
}
