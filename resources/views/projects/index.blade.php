@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')
@section('title')
    Projects
@endsection

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <h2>Projects</h2>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">Add Project</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">

            @if($projects->isEmpty())
                <div class="col-12">
                    <p class="text-muted">No projects yet. Create your first one!</p>
                </div>
            @endif

            @foreach($projects as $project)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $project->name }}</h5>
                            <p class="card-text">{{ $project->description }}</p>

                            <div class="d-flex gap-2 mt-2 mb-3">
                                <span class="badge bg-secondary">To Do: {{ $project->to_do_tasks }}</span>
                                <span class="badge bg-warning">In Progress: {{ $project->in_progress }}</span>
                                <span class="badge bg-success">Done: {{ $project->done }}</span>
                            </div>


                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary"> <i class="bi bi-eye"></i> </a>
                            @if(Auth::user()->can('update', $project))
                                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning"> <i class="bi bi-pencil-square"></i> </a>
                            @endif

                            @if(Auth::user()->can('delete', $project))
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this project?')"> <i class="bi bi-trash"></i> </button>
                                </form>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection
