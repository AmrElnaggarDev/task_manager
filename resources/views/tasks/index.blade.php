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

        {{-- Filter bar --}}
        <div class="bg-white shadow-sm rounded p-3 mb-4 d-flex gap-2 flex-wrap">
            <a href="{{route('tasks.index', $project->id)}}" class="btn btn-sm btn-dark">All</a>
            <a href="{{route('tasks.index', ['project' => $project->id, 'status' => 'todo'])}}" class="btn btn-sm btn-outline-primary">To Do</a>
            <a href="{{route('tasks.index', ['project' => $project->id, 'status' => 'in_progress'])}}" class="btn btn-sm btn-outline-warning">In Progress</a>
            <a href="{{route('tasks.index', ['project' => $project->id, 'status' => 'done'])}}" class="btn btn-sm btn-outline-success">Done</a>
        </div>

        {{-- Empty state --}}
        @if($tasks->isEmpty())
            <div class="text-center py-5 bg-white rounded shadow-sm">
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
                                <a href="{{route('tasks.edit', $task->id)}}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></a>
                                <form action="{{route('tasks.destroy', $task->id)}}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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
