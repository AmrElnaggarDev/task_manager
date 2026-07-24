<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskChecklistItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskChecklistItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskChecklistItem $taskChecklistItem): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Task $task): bool
    {
        $project = $task->project;

        if (!$project) return false;

        $canAccessProject = $user->id === $project->owner_id
            || $project->members()
            ->where('user_id', $user->id)
            ->exists();

        $canManageChecklist = $user->id === $project->owner_id
            || $user->id == $task->created_by
            || $user->id == $task->assigned_to;

        return $canAccessProject && $canManageChecklist;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(
        User $user,
        TaskChecklistItem $taskChecklistItem
    ): bool {
        $task = $taskChecklistItem->task;
        $project = $task?->project;

        if (! $task || ! $project) {
            return false;
        }

        $canAccessProject = $user->id === $project->owner_id
            || $project->members()
                ->where('user_id', $user->id)
                ->exists();

        $canUpdateChecklistItem =
            $user->id === $project->owner_id
            || $user->id === $task->created_by
            || $user->id === $task->assigned_to;

        return $canAccessProject && $canUpdateChecklistItem;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskChecklistItem $checklistItem): bool
    {
        $task = $checklistItem->task;
        $project = $task?->project;

        if (! $task || ! $project) {
            return false;
        }

        $canAccessProject = $user->id === $project->owner_id
            || $project->members()
                ->where('user_id', $user->id)
                ->exists();

        $canDelete = $user->id === $project->owner_id
            || $user->id === $task->created_by
            || $user->id === $checklistItem->created_by;

        return $canAccessProject && $canDelete;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskChecklistItem $taskChecklistItem): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskChecklistItem $taskChecklistItem): bool
    {
        return false;
    }
}
