<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $tasks = $project->tasks()
            ->when(\request('status'), fn($query) => $query->where('status', \request('status')))
            ->orderBy('status')
            ->get();
        $users = $project->members()->get();
        return view('tasks.index', compact('tasks', 'users', 'project'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        $users = $project->members()->get();
        return view('tasks.create', compact('project', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        $attributes = $request->validate([
            'assigned_to' => 'nullable|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:todo,in_progress,done',
            'priority' => 'required|string|in:low,medium,high',
            'deadline' => 'nullable|date',
        ]);

        $attributes['project_id'] = $project->id;
        $attributes['created_by'] = auth()->id();

        $project->tasks()->create($attributes);

        return redirect()->route('tasks.index', $project)->with('success', 'Task created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $project = $task->project;

        $this->authorize('view', [$task, $project]);

        $users = $project->members()->get();
        return view('tasks.show', compact('task', 'project', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize ('update', $task);

        $project = $task->project;
        $users = $project->members()->get();
        return view('tasks.edit', compact('task', 'project', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize ('update', $task);

        $attributes = $request->validate([
            'title' => 'required|string|max:255|',
            'description' => 'nullable|string|',
            'status' => 'required|string|in:todo,in_progress,done',
            'priority' => 'required|string|in:low,medium,high',
            'deadline' => 'nullable|date',
            ]);

        $task->update($attributes);
        return redirect()->route('tasks.index', $task->project_id)->with('success', 'Task updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize ('delete', $task);

        $task->delete();
        return redirect()->route('tasks.index', $task->project_id)->with('success', 'Task deleted.');    }
}
