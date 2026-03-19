@extends('layouts.app')
@section('title')
    {{$project->name}}
@endsection

@section('content')
    <div class="container pb-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <h2 class="mb-0">{{$task->title}}</h2>
            <div class="d-flex gap-2">
                @can('update', $task)
                    <a href="{{route('tasks.edit', $task->id)}}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                @endcan

                @can('delete', $task)
                        <form action="{{route('tasks.destroy', $task->id)}}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this task?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    @endcan
            </div>
        </div>

        <div class="row">

            {{-- Left column: main info --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Description</h5>
                        @if($task->description)
                            <p class="text-muted" style="line-height: 1.8;">{{ $task->description }}</p>
                        @else
                            {{'No Description Provided.'}}
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right column: meta info --}}
            <div class="col-md-4">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted small" style="width:40%">Status</td>
                                <td>
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
                            </tr>

                            <tr>
                                <td class="text-muted small">Priority</td>
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
                            </tr>

                            <tr>
                                <td class="text-muted small">Deadline</td>
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
                            </tr>

                            <tr>
                                <td class="text-muted small">Assigned To</td>
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
                            </tr>

                            <tr>
                                <td class="text-muted small">Created By</td>
                                <td>
                                    @if($task->creator)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                 style="width:30px;height:30px;font-size:12px;flex-shrink:0;">
                                                {{ strtoupper(substr($task->creator->name, 0, 1)) }}
                                            </div>
                                            <span class="small">{{ $task->creator->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Unassigned</span>
                                    @endif
                                </td>
                            </tr>


                            <tr>
                                <td class="text-muted small">Project</td>
                                <td>
                                    <a href="{{ route('projects.show', $project->id) }}" class="small">
                                        {{ $project->name }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <a href="{{route('tasks.index', $project->id)}}" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Back to Tasks
        </a>

    </div>
@endsection
