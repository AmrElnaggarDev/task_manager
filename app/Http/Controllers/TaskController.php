<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Project $project)
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $assignees = $project->all_possible_assignees;
        $allowedAssigneeIds = $project->all_possible_assignees->pluck('id')->toArray();

        $query = $project->tasks()
            ->with(['assignee', 'creator']);

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('priority'), function ($q) use ($request) {
            $q->where('priority', $request->priority);
        });

        $query->when($request->filled('assignee'), function ($q) use ($request, $allowedAssigneeIds) {
            if ($request->assignee == 'unassigned') {
                $q->whereNull('assigned_to');
            }else if (in_array($request->assignee, $allowedAssigneeIds)) {
                $q->where('assigned_to', $request->assignee);
            }
        });


        $query->when($request->filled('deadline'), function ($q) use ($request) {
            $today = Carbon::today()->toDateString();

            switch ($request->deadline) {
                case 'overdue' :
                    $q->where('deadline', '<', $today)
                        ->where ('status', '!=', 'done');
                    break;
                case 'today' :
                    $q->whereDate('deadline',$today);
                    break;
                case 'upcoming' :
                    $q->where('deadline', '>', $today)
                    ->where ('status', '!=', 'done');
                    break;
                case 'no_deadline' :
                    $q->whereNull('deadline');
                    break;
            }
        });

        $sort = $request->query('sort');

        $allowedSorts = [
            'newest'        => ['type' => 'normal', 'column' => 'created_at', 'direction' => 'desc'],
            'oldest'        => ['type' => 'normal', 'column' => 'created_at', 'direction' => 'asc'],
            'deadline'      => ['type' => 'raw',    'sql' => 'ISNULL(deadline) ASC, deadline ASC'],
            'deadline_desc' => ['type' => 'normal', 'column' => 'deadline',   'direction' => 'desc'],
            'priority'      => ['type' => 'raw',    'sql' => "FIELD(priority, 'high', 'medium', 'low') ASC"],
            'status'        => ['type' => 'normal', 'column' => 'status',     'direction' => 'asc'],
        ];

        $query->when($request->filled('sort') && array_key_exists($sort, $allowedSorts),
            function ($q) use ($sort, $allowedSorts) {
                $sortConfig = $allowedSorts[$sort];

                if ($sortConfig['type'] === 'raw') {
                    return $q->orderByRaw($sortConfig['sql']);
                }

                return $q->orderBy($sortConfig['column'], $sortConfig['direction']);
            },
            function ($q) {
                return $q->latest();
            }
        );

        $tasks = $query->get();
        $filters = $request->all();

        return view('tasks.index', compact('tasks', 'assignees', 'project', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        // assignees = owner + members
        $assignees = $project->all_possible_assignees;
        return view('tasks.create', compact('project', 'assignees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        $allowedAssigneeIds = $project->all_possible_assignees->pluck('id')->toArray();

        $attributes = $request->validate([
            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
                Rule::in($allowedAssigneeIds),
            ],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:todo,in_progress,done',
            'priority' => 'required|string|in:low,medium,high',
            'deadline' => 'nullable|date',

        ], [
            'assigned_to.in' => 'The selected assignee must be the project owner or a project member.',
        ]);



        $attributes['project_id'] = $project->id;
        $attributes['created_by'] = auth()->id();

        $task = $project->tasks()->create($attributes);

        Activity::log($project, auth()->user(), 'task_created', "Created Task: {$task->title}");

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

        $comments = $task->comments()->with('user')->oldest()->get();
        return view('tasks.show', compact('task', 'project', 'users', 'comments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize ('update', $task);

        $project = $task->project;
        $assignees = $project->all_possible_assignees;
        return view('tasks.edit', compact('task', 'project', 'assignees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize ('update', $task);

        $project = $task->project;
        $allowedAssigneeIds = $project->all_possible_assignees->pluck('id')->toArray();

        $attributes = $request->validate([
            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
                Rule::in($allowedAssigneeIds),
            ],
            'title' => 'required|string|max:255|',
            'description' => 'nullable|string|',
            'status' => 'required|string|in:todo,in_progress,done',
            'priority' => 'required|string|in:low,medium,high',
            'deadline' => 'nullable|date',
            ],[
                'assigned_to.in' => 'The selected assignee must be the project owner or a project member.',
        ]);

        $oldStatus = $task->getOriginal('status');

        $task->update($attributes);

        if ($task->wasChanged('status')) {
            $fromStatus = str($oldStatus)->replace('_', ' ')->title();
            $toStatus = str($task->status)->replace('_', ' ')->title();

            Activity::log(
                $project->id,
                auth()->user(),
                'task_status_updated',
                "changed task \"{$task->title}\" status from {$fromStatus} to {$toStatus}"
        );}

        return redirect()->route('tasks.index', $task->project_id)->with('success', 'Task updated.');
    }

    public function updateStatus (Request $request, Task $task)
    {
        $this->authorize ('updateStatus', $task);
        $attributes = $request->validate([
            'status' => 'required|string|in:todo,in_progress,done',
        ]);

        $oldStatus = $task->status;

        $task->update([
            'status' => $attributes['status'],
        ]);

        if ($task->wasChanged('status')) {
            $fromStatus = str($oldStatus)->replace('_', ' ')->title();
            $toStatus = str($task->status)->replace('_', ' ')->title();

            Activity::log(
                $task->project_id,
                auth()->user(),
                'task_status_updated',
                "changed task \"{$task->title}\" status from {$fromStatus} to {$toStatus}"
            );
        }
        return back()->with('success', 'Task Status updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize ('delete', $task);

        $project = $task->project;
        $title = $task->title;

        $task->delete();

        Activity::log ($project->id, auth()->user(), 'task_deleted', "Deleted Task: {$title}");
        return redirect()->route('tasks.index', $task->project_id)->with('success', 'Task deleted.');    }
}
