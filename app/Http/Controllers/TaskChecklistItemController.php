<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Models\TaskChecklistItem;
use Illuminate\Http\Request;

class TaskChecklistItemController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('create', [TaskChecklistItem::class, $task]);

        $attributes = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);




        $item = $task->checklistItems()->create([
            'created_by' => auth()->id(),
            'title' => $attributes['title'],
            'is_completed' => false,
        ]);

        Activity::log(
            $task->project_id,
            auth()->user(),
            'checklist_item_created',
            "added checklist item \"{$item->title}\" to task \"{$task->title}\""
        );

        return back()->with('success', 'Checklist item created');

    }

    public function toggle (TaskChecklistItem $checklistItem)
    {
        $this->authorize('update', $checklistItem);

        $is_completed = !  $checklistItem->is_completed;
        $checklistItem->update([
            'is_completed' => $is_completed,
            'completed_at' => $is_completed ? now() : null,
        ]);

        $task = $checklistItem->task;

        Activity::log(
            $task->project_id,
            auth()->user(),
            $is_completed ? 'checklist_item_completed' : 'checklist_item_reopened',
            $is_completed
                ? "completed checklist item \"{$checklistItem->title}\""
                : "reopened checklist item \"{$checklistItem->title}\""
        );

        return back()->with('success', 'Checklist item updated');
    }

    public function destroy(TaskChecklistItem $checklistItem)
    {
        $this->authorize('delete', $checklistItem);

        $task = $checklistItem->task;
        $title = $checklistItem->title;

        $checklistItem->delete();

        Activity::log(
            $task->project_id,
            auth()->user(),
            'checklist_item_deleted',
            "deleted checklist item \"{$title}\" from task \"{$task->title}\""
        );

        return back()->with('success', 'Checklist item deleted');
    }
}
