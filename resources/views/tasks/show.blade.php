@php
    use App\Models\TaskAttachment;
    use Illuminate\Support\Str;
@endphp
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
                    <a href="{{route('tasks.edit', $task->id)}}" class="btn btn-warning"><i
                            class="bi bi-pencil-square"></i> Edit</a>
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

                {{-- Task Attachments --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">
                                    <i class="bi bi-paperclip me-1"></i>
                                    Attachments
                                </h5>

                                <p class="text-muted small mb-0">
                                    Files shared with this task.
                                </p>
                            </div>

                            <span class="badge bg-secondary">
                                {{$attachments->count()}}
                            </span>
                        </div>

                        @can('create', [TaskAttachment::class, $task])
                            <form
                                action="{{route('tasks.attachments.store', $task->id)}}"
                                method="POST"
                                enctype="multipart/form-data"
                                class="mb-4"
                            >
                                @csrf

                                <div class="input-group">
                                    <input
                                        type="file"
                                        name="attachment"
                                        class="form-control @error('attachment') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.zip"
                                    >

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-upload me-1"></i>
                                        Upload
                                    </button>
                                </div>

                                @error('attachment')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                                @enderror

                                <div class="form-text">
                                    Maximum size: 5 MB.
                                </div>
                            </form>
                        @endcan

                        <div class="list-group list-group-flush border-top pt-2">
                            @forelse($attachments as $attachment)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="d-flex align-items-center overflow-hidden me-3">
                                        <i class="bi bi-file-earmark-text fs-3 text-primary me-3"></i>

                                        <div class="text-truncate">
                                            <a href="{{route('attachments.download', $attachment)}}"
                                               class="fw-bold text-decoration-none text-dark d-block text-truncate"
                                               title="{{ $attachment->original_name }}" >
                                                {{ $attachment->original_name }}
                                            </a>

                                            <small class="text-muted d-block" style="font-size: 0.8rem;">
                                                <i class="bi bi-person me-1"></i> {{ $attachment->uploader->name ?? 'Unknown' }}

                                                <span class="mx-1">•</span>

                                                <i class="bi bi-clock me-1"></i>{{ $attachment->created_at->diffForHumans() }}

                                                <span class="mx-1">•</span>


                                                <i class="bi bi-hdd me-1"></i>
                                                @if($attachment->size >= 1048576)
                                                    {{ number_format($attachment->size / 1048576, 2) }} MB
                                                @else
                                                    {{ number_format($attachment->size / 1024, 1) }} KB
                                                @endif
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('attachments.download', $attachment) }}"
                                           class="btn btn-sm btn-light border"
                                           title="Download File">
                                            <i class="bi bi-download"></i>
                                        </a>

                                        <!-- 6. Delete Form & Button -->
                                    @can('delete', $attachment)
                                        <form action="{{ route('attachments.destroy', $attachment) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this file?')"
                                                    title="Delete File">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>

                                </div>

                            @empty
                                <p class="text-muted text-center my-3 small">
                                    No attachments uploaded yet.
                                </p>

                                @endforelse
                        </div>

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
                                        'todo' => 'bg-secondary',
                                        'in_progress' => 'bg-warning',
                                        'done' => 'bg-success',
                                        default => 'bg-secondary',
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
                                        'high' => 'bg-danger',
                                        'medium' => 'bg-warning',
                                        'low' => 'bg-success',
                                        default => 'bg-secondary',
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
                                            <div
                                                class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
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
                                            <div
                                                class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
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

                {{-- Task Status Update (تم نقله هنا داخل الـ col-md-4) --}}
                @can('updateStatus', $task)
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">

                            <h5 class="card-title mb-1">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Update Status
                            </h5>

                            <p class="text-muted small">
                                Change the current progress state of this task.
                            </p>

                            <form
                                action="{{ route('tasks.status.update', $task) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        Status
                                    </label>

                                    <select
                                        name="status"
                                        id="status"
                                        class="form-select @error('status') is-invalid @enderror"
                                    >
                                        <option
                                            value="todo" {{ old('status', $task->status) === 'todo' ? 'selected' : '' }}>
                                            To Do
                                        </option>
                                        <option
                                            value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>
                                            In Progress
                                        </option>
                                        <option
                                            value="done" {{ old('status', $task->status) === 'done' ? 'selected' : '' }}>
                                            Done
                                        </option>
                                    </select>

                                    @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Update Status
                                </button>
                            </form>

                        </div>
                    </div>
                @endcan

            </div>

        </div>

        {{-- Comments Section --}}
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">Comments</h5>
                                <p class="text-muted small mb-0">
                                    Discuss updates and notes related to this task.
                                </p>
                            </div>
                        </div>

                        {{-- Add Comment Form --}}
                        <form action="{{route('tasks.comments.store', $task)}}" method="POST" class="mb-4">
                            @csrf

                            <div class="mb-3">
                                <label for="comment_body" class="form-label">Add a comment</label>

                                <textarea name="body"
                                          id="comment_body"
                                          rows="3"
                                          class="form-control"
                                          placeholder="Write your comment here..."></textarea>

                                @error('body')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-chat-left-text"></i>
                                Add Comment
                            </button>
                        </form>

                        <hr>

                        {{-- Comments List --}}
                        <div>

                            @forelse($comments as $comment)
                                {{-- Comment item example --}}
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3">

                                        <div class="d-flex gap-2">
                                            <div
                                                class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                style="width:35px;height:35px;font-size:13px;flex-shrink:0;">
                                                {{ Str::substr(strtoupper($comment->user->name), 0, 1) }}
                                            </div>

                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
<span class="fw-semibold small">
{{ $comment->user->name }}
</span>

                                                    <span class="text-muted small">
{{ $comment->created_at->diffForHumans() }}
</span>
                                                </div>

                                                <p class="mb-0 text-muted" style="line-height: 1.7;">
                                                    {{ $comment->body }}
                                                </p>
                                            </div>
                                        </div>

                                        @can('delete', $comment)
                                            <form action="{{route('tasks.comments.destroy', [$task, $comment])}}"
                                                  method="POST" class="m-0">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Delete this comment?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan

                                    </div>
                                </div>

                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-chat-dots fs-2 d-block mb-2"></i>
                                    No comments yet.
                                </div>

                            @endforelse

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <a href="{{route('tasks.index', $project->id)}}" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Back to Tasks
        </a>

    </div>
@endsection
