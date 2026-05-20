<?php

namespace App\Http\Controllers;

use App\Models\Activity;
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

         Activity::log($project, auth()->user(), 'comment_added', "Commented on task {$task->title} ");

        return back()->with('success', 'Comment added successfully.');
    }

    public function destroy(Task $task, Comment $comment)
    {
        abort_if($comment->task_id != $task->id, 404);

        $this->authorize('delete', $comment);

        $project = $task->project;

        $comment->delete();

        Activity::log($project, auth()->user(), 'comment_deleted', "Deleted a comment from {$task->title} ");

        return back()->with('success', 'Comment deleted successfully.');

    }
}
