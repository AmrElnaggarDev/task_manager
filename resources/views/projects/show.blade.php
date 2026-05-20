@php use Illuminate\Support\Str; @endphp
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

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="row">
            {{-- العمود الأيمن العريض (تفاصيل المشروع) --}}
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

                    <div class="d-flex gap-2 mt-3 p-3 border-top">
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

            {{-- العمود الأيسر (الأعضاء + سجل الأنشطة) --}}
            <div class="col-md-5">
                {{-- Team Members --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">Team Members</h5>
                                <p class="text-muted small mb-0">
                                    Manage users who can access this project.
                                </p>
                            </div>

                            @can('update', $project)
                                <button type="button"
                                        class="btn btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addMemberModal">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                            @endcan
                        </div>

                        {{-- Owner info --}}
                        <div class="border rounded p-3 mb-3 bg-light">
                            <small class="text-muted d-block mb-2">Project Owner</small>

                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center"
                                     style="width:35px;height:35px;font-size:13px;flex-shrink:0;">
                                    {{Str::substr(strtoupper($owner->name), 0, 1)}}
                                </div>

                                <div>
                                    <div class="fw-semibold small">
                                        {{$owner->name}}
                                    </div>
                                    <div class="text-muted small">
                                        {{$owner->email}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Members list --}}
                        <div class="row">
                            @forelse($teamMembers as $teamMember)
                                <div class="col-12">
                                    <div class="border rounded p-3 mb-2">
                                        <div class="d-flex justify-content-between align-items-center gap-3">

                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                     style="width:35px;height:35px;font-size:13px;flex-shrink:0;">
                                                    {{Str::substr (strtoupper($teamMember->name), 0, 1)}}
                                                </div>

                                                <div>
                                                    <div class="fw-semibold small">
                                                        {{$teamMember->name}}
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $teamMember->email }}
                                                    </div>
                                                </div>
                                            </div>

                                            @can('update', $project)
                                                <form action="{{route('projects.members.destroy', [$project, $teamMember])}}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Remove this member from the project?')">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-people fs-2 d-block mb-2"></i>
                                        No team members yet.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">Activity Log</h5>
                                <p class="text-muted small mb-0">
                                    Recent updates and actions inside this project.
                                </p>
                            </div>
                        </div>

                        @forelse($activities as $activity)
                            <div class="border-start border-primary ps-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <p class="mb-1 small">
                                            <strong>{{ $activity->user->name ?? 'System' }}</strong>
                                            {{ $activity->description }}
                                        </p>
                                        <span class="text-muted small" style="font-size: 12px;">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <span class="badge bg-light text-dark border">
                                        {{ $activity->type }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-clock-history fs-2 d-block mb-2"></i>
                                No activity yet.
                            </div>
                        @endforelse
                    </div>
                </div>


            </div>
        </div>
    </div>

    {{-- Add Member Modal --}}
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">
                        Add Team Member
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{route('projects.members.store', $project)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="member_email" class="form-label">User Email</label>

                            <input type="email"
                                   name="email"
                                   id="member_email"
                                   class="form-control"
                                   placeholder="Enter registered user email"
                                   value="{{old('email')}}">

                            <small class="text-muted">
                                The user must already have an account.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i>
                            Add Member
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
