@extends('layouts.app')
@section('title')
    {{$project->name}} - Tasks
@endsection

@section('content')
    <div class="container pb-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <div>
                <h2 class="mb-0">{{$project->name}}</h2>
                <small class="text-muted">{{$tasks->count()}} tasks</small>
            </div>
            <a href="{{route('tasks.create', $project->id)}}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> New Task
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        {{-- Advanced Filters --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                <form action="{{route('tasks.index', $project->id)}}" method="GET">
                    <div class="row g-3 align-items-end">

                        {{-- Search --}}
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   class="form-control"
                                   placeholder="Search task title..."
                                   value="{{$filters['search'] ?? ''}}">
                        </div>

                        {{-- Status --}}
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All</option>
                                <option value="todo" @selected(($filters['status'] ?? '')== 'todo')>To Do</option>
                                <option value="in_progress" @selected(($filters['status'] ?? '')== 'in_progress')>In Progress</option>
                                <option value="done" @selected(($filters['status'] ?? '')== 'done')>Done</option>
                            </select>
                        </div>

                        {{-- Priority --}}
                        <div class="col-md-2">
                            <label for="priority" class="form-label">Priority</label>
                            <select name="priority" id="priority" class="form-select">
                                <option value="">All</option>
                                {{-- OPTIONS: low, medium, high --}}
                                <option value="low" @selected(($filters['priority'] ?? '')== 'low')>Low</option>
                                <option value="medium" @selected(($filters['priority'] ?? '')== 'medium')>Medium</option>
                                <option value="high" @selected(($filters['priority'] ?? '')== 'high')>High</option>
                            </select>
                        </div>

                        {{-- Assignee --}}
                        <div class="col-md-3">
                            <label for="assignee" class="form-label">Assignee</label>
                            <select name="assignee" id="assignee" class="form-select">
                                <option value="">All</option>
                                <option value="unassigned" @selected(($filters['assignee'] ?? '') == 'unassigned')>
                                    Unassigned
                                </option>

                                @foreach($assignees as $assignee)
                                    <option value="{{$assignee->id}}"  @selected(($filters['assignee'] ?? '') == $assignee->id)>
                                        {{$assignee->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Deadline --}}
                        <div class="col-md-2">
                            <label for="deadline" class="form-label">Deadline</label>
                            <select name="deadline" id="deadline" class="form-select">
                                <option value="">All</option>
                                <option value="overdue"  @selected(($filters['deadline'] ?? '') == 'overdue')>Overdue</option>
                                <option value="today"  @selected(($filters['deadline'] ?? '') == 'today')>Due Today</option>
                                <option value="upcoming"  @selected(($filters['deadline'] ?? '') == 'upcoming')>Upcoming</option>
                                <option value="no_deadline"  @selected(($filters['deadline'] ?? '') == 'no_deadline')>No Deadline</option>
                            </select>
                        </div>


                        {{-- Sort Dropdown  --}}
                        <div class="col-md-4 mt-3">
                            <label for="sort" class="form-label">Sort Order</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-sort-down"></i>
                                </span>
                                <select name="sort" id="sort" class="form-select border-start-0" onchange="this.form.submit()">
                                    <option value="" @selected(empty($filters['sort'] ?? ''))>Default (Newest First)</option>
                                    <option value="newest" @selected(($filters['sort'] ?? '') == 'newest')>Newest Tasks</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') == 'oldest')>Oldest Tasks</option>
                                    <option value="deadline" @selected(($filters['sort'] ?? '') == 'deadline')>Deadline (Closest First)</option>
                                    <option value="deadline_desc" @selected(($filters['sort'] ?? '') == 'deadline_desc')>Deadline (Furthest First)</option>
                                    <option value="priority" @selected(($filters['sort'] ?? '') == 'priority')>Priority</option>
                                    <option value="status" @selected(($filters['sort'] ?? '') == 'status')>Status</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 d-none d-md-block"></div>

                        {{-- Buttons --}}
                        <div class="col-md-2 mt-3 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary w-50">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>

                            <a href="{{route('tasks.index', $project->id)}}" class="btn btn-outline-secondary w-50" title="Reset Filters">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>


        {{-- Empty state --}}
        @if($tasks->isEmpty())
            <div class="text-center py-5 bg-white rounded shadow-sm mb-4">
                <i class="bi bi-clipboard2 fs-1 text-muted"></i>
                <p class="text-muted mt-3">No tasks yet.</p>
            </div>
        @endif

        {{-- Tasks table --}}
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Assigned To</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>
                                <span class="fw-500">{{ $task->title }}</span>
                                @if($task->description)
                                    <p class="text-muted small mb-0">{{ Str::limit($task->description, 50) }}</p>
                                @endif
                            </td>
                            <td>
                                @if($task->assignee)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width:30px;height:30px;font-size:12px;flex-shrink:0;">
                                            {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                        </div>
                                        <span class="small">{{ $task->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $priorityClass = match ($task->priority){
                                         'high'   => 'bg-danger',
                                            'medium' => 'bg-warning',
                                            'low'    => 'bg-success',
                                            default  => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{$priorityClass}}">{{$task->priority}}</span>
                            </td>
                            <td>
                                {{-- STATUS BADGE --}}
                                @php
                                    $statusClass = match ($task->status) {
                                        'todo'        => 'bg-secondary',
                                            'in_progress' => 'bg-warning',
                                            'done'        => 'bg-success',
                                            default       => 'bg-secondary',
                                    };

                                    $statusLabel = match ($task->status) {
                                        'todo' => 'To Do',
                                        'in_progress' => 'In Progress',
                                        'done' => 'Done',
                                        default => $task->status,
                                    };

                                @endphp
                                <span class="badge {{$statusClass}}">{{$statusLabel}}</span>
                            </td>
                            <td>
                                @if($task->deadline)
                                    @php
                                        $isOverdue = \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status !== 'done';
                                    @endphp
                                    <span class="small {{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                        <i class="bi bi-calendar{{ $isOverdue ? '-x' : '' }} me-1"></i>
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>


                            <td class="text-end">
                                <a href="{{route('tasks.show', $task->id)}}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                @can('update', $task)
                                    <a href="{{route('tasks.edit', $task->id)}}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></a>
                                @endcan

                                @can('delete', $task)
                                    <form action="{{route('tasks.destroy', $task->id)}}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>

        {{-- Back link --}}
        <div class="mt-4">
            <a href="{{ route('projects.index') }}" class="text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Back to Projects
            </a>
        </div>

    </div>
@endsection
