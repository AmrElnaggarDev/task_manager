<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;

class TaskCommentController extends Controller
{
    public function store(Task $task)
    {
        $project = $task->project;
        $this->authorize('view', [$task, $project]);

        \request()->validate([
            'body' => 'required|string|max:1000'
        ]);

         Comment::create ([
            'body' => \request('body'),
            'task_id' => $task->id,
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Comment added successfully.');
    }

    public function destroy(Task $task, Comment $comment)
    {
        abort_if($comment->task_id != $task->id, 404);

        $this->authorize('delete', $comment);
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');

    }
}
