@extends('layouts.app')
@section('title')
    {{$project->name}}
@endsection

@section('content')
    <div class="container mb-3">

        <h2 class="mb-4 shadow-sm p-3 rounded bg-white">Edit Task</h2>

        <div class="card border-0 shadow-sm m-auto" style="max-width: 650px;">
            <div class="card-body">
                <form action="{{route('tasks.update', $task->id)}}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{$task->title}}" required>
                    </div>

                    @error('title')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" >{{$task->description}}</textarea>

                        @error('description')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select"  required>
                            <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                        </select>

                        @error('status')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Priority --}}
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                        </select>

                        @error('priority')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Assign To --}}
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <select name="assigned_to" id="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>

                        @error('assigned_to')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deadline --}}
                    <div class="mb-3">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input type="date" name="deadline" id="deadline" class="form-control" value="{{$task->deadline}}">

                        @error('deadline')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Edit Task</button>
                        <a href="{{route('tasks.index', $project->id)}}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection
