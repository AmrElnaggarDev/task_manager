<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function store (Request $request, Task $task)
    {
        $this->authorize('create', [TaskAttachment::class, $task]);

        $attibutes = $request->validate([
            'attachment' => 'required|file|max:5120|mimes:jpeg,jpg,png,pdf,doc,docx,txt,zip'
        ]);
        $file = $attibutes['attachment'];

        $storedName = (string) str()->uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs (
            "task-attachments/{$task->id}", $storedName,
        );

        try {
            $attachment = $task->attachments()->create([
                'uploaded_by' => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        } catch (\Throwable $exception) {
            Storage::delete($path);
            throw $exception;
        }

        Activity::log(
            $task->project_id,
            auth()->user(),
            'attachment_uploaded',
            "uploaded attachment \"{$attachment->original_name}\" to task \"{$task->title}\"",
        );

        return back()->with('success', 'Attachment uploaded successfully');
    }

    public function download (TaskAttachment $attachment)
    {
        $this->authorize('view', $attachment) ;

        abort_unless(
            Storage::exists($attachment->path),404
        );

        return Storage::download($attachment->path, $attachment->original_name);
    }

    public function destroy(TaskAttachment $attachment)
    {
        $this->authorize('delete', $attachment);

        $task = $attachment->task;
        $originalName = $attachment->original_name;
        $path = $attachment->path;

        Storage::delete($path);
        $attachment->delete();

        Activity::log(
            $task->project_id,
            auth()->user(),
            'attachment_deleted',
            "deleted attachment \"{$originalName}\" from task \"{$task->title}\""
        );

        return back()->with('success', 'Attachment deleted successfully');
    }
}
