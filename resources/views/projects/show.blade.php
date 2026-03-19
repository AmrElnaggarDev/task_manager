@extends('layouts.app')
@section('title')
    {{ $project->name }} - Project Details
@endsection
@section('content')
    <div class="container">
        <h2 class="mb-4 shadow-sm p-3 rounded bg-white text-center"> {{ $project->name }}</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $project->name }}</h5>
                        <p class="card-text">{{ $project->description }}</p>


                        <h5 class="mt-4">Project Progress</h5>
                        @php
                            $tasks = $project->tasks;
                            $totalTasks = $tasks->count();
                            $completedTasks = $tasks->where('status', 'done')->count();
                            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                        @endphp
                        <div class="progress mb-4">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;"
                                 aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                {{ round($progress) }}%</div>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Back to Projects</a>

                        @can('update', $project)
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit Project
                            </a>
                        @endcan

                        @can('delete', $project)
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this project?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title"> Team Members </h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addMemberModal"> <i class="bi bi-plus-circle"></i> </button>
                        </div>

                        <div class="row">
                            @foreach ($teamMembers as $user)
                                <div class="col-12">
                                    <div class="card mb-3">
                                        <div class="row g-0">
                                            <div class="col-md-12">
                                                <div class="card-body">
                                                    <p class="card-title fw-bolder">{{ $user->name }}</p>
                                                    <p class="card-text">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
