@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str
@endphp
@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="container pb-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <div>
                <h2 class="mb-1">Dashboard</h2>
                <p class="text-muted mb-0 small">
                    Welcome back, {{$user->name}}
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{route('projects.create')}}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Project
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row mb-4">

            {{-- Projects Count --}}
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1">Projects</small>
                            <h3 class="mb-0 fw-bold">
                                {{$projectsCount}}
                            </h3>
                        </div>

                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                             style="width:45px;height:45px;flex-shrink:0;">
                            <i class="bi bi-folder fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Tasks Count --}}
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1">Total Tasks</small>
                            <h3 class="mb-0 fw-bold">
                                {{$tasksCount}}
                            </h3>
                        </div>

                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                             style="width:45px;height:45px;flex-shrink:0;">
                            <i class="bi bi-check2-square fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- To Do Tasks Count --}}
            <div class="col-md-6 col-lg-2 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">To Do</small>
                        <h4 class="mb-0 fw-bold">
                            {{$todoTasksCount}}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- In Progress Tasks Count --}}
            <div class="col-md-6 col-lg-2 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">In Progress</small>
                        <h4 class="mb-0 fw-bold text-warning">
                            {{$inProgressTasksCount}}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Done Tasks Count --}}
            <div class="col-md-6 col-lg-2 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Done</small>
                        <h4 class="mb-0 fw-bold text-success">
                            {{$doneTasksCount}}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">

            {{-- Left column: Latest Projects --}}
            <div class="col-md-5">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Latest Projects</h5>

                        <a href="{{route('projects.index')}}" class="small text-decoration-none">
                            View All
                        </a>
                    </div>

                    <div class="card-body p-0">

                        {{-- LOOP LATEST PROJECTS HERE --}}
                        @forelse($latestProjects as $project)
                            {{-- Project item example --}}
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{route('projects.show', $project->id)}}" class="text-dark text-decoration-none">
                                                {{$project->name}}
                                            </a>
                                        </h6>

                                        <p class="text-muted small mb-0">
                                            @if ($project->description)
                                                {{Str::limit($project->description, 70)}}
                                            @else
                                                 No Description Added.
                                            @endif
                                        </p>
                                    </div>

                                    <span class="badge bg-light text-dark">
                                    {{$project->created_at->diffForHumans()}}
                                </span>
                                </div>
                            </div>

                            @empty
                                <div class="p-4 text-center text-muted">
                                <i class="bi bi-folder-plus fs-2 d-block mb-2"></i>
                                No projects yet.
                            </div>
                        @endforelse





                    </div>
                </div>

            </div>

            {{-- Right column: Latest Tasks --}}
            <div class="col-md-7">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Latest Tasks</h5>

                        <span class="small text-muted">
                            Recent activity
                        </span>
                    </div>

                    <div class="card-body p-0">

                        @forelse($latestTasks as $task)
                            {{-- Task item example --}}
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-3">

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{route('tasks.show', $task->id)}}" class="text-dark text-decoration-none">
                                                {{$task->title}}
                                            </a>
                                        </h6>

                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-folder me-1"></i>
                                            {{$task->project->name ?? 'No project'}}
                                        </div>

                                        <div class="d-flex flex-wrap gap-2">

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

                                            @php
                                                $priorityClass = match ($task->priority){
                                                'high'   => 'bg-danger',
                                                'medium' => 'bg-warning',
                                                'low'    => 'bg-success',
                                                default  => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{$priorityClass}}">{{$task->priority}}</span>

                                            @if($task->deadline)
                                                <span class="badge bg-light text-dark">
                                            <i class="bi bi-calendar-event me-1"></i>
                                                    {{Carbon::parse($task->deadline)->format('M d, Y')}}
                                             </span>
                                            @endif


                                        </div>
                                    </div>

                                    <span class="small text-muted text-nowrap">
                                    {{$task->created_at? $task->created_at->diffForHumans() : 'No DateTime'}}
                                </span>

                                </div>
                            </div>

                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-check2-square fs-2 d-block mb-2"></i>
                                No tasks yet.
                            </div>
                        @endforelse


                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
