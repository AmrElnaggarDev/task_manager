<?php

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

it('allows the comment owner to delete the comment', function () {
    $owner = User::factory()->create();
    $commentOwner = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($commentOwner);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);
    $comment = Comment::create([
        'task_id' => $task->id,
        'user_id' => $commentOwner->id,
        'body' => 'comment body',
    ]);

    $response = $this->actingAs($commentOwner)
        ->delete(route('tasks.comments.destroy', [$task, $comment]));
    $response->assertRedirect();

    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

it('allows the project owner to delete any comment', function () {
    $owner = User::factory()->create();
    $commentOwner = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($commentOwner);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);
    $comment = Comment::create([
        'task_id' => $task->id,
        'user_id' => $commentOwner->id,
        'body' => 'comment body',
    ]);

    $response = $this->actingAs($owner)
        ->delete(route('tasks.comments.destroy', [$task, $comment]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

it('forbids a project member from deleting another users comment', function () {
    $owner = User::factory()->create();
    $commentOwner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach([$commentOwner->id, $member->id]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);
    $comment = Comment::create([
        'task_id' => $task->id,
        'user_id' => $commentOwner->id,
        'body' => 'comment body',
    ]);

    $response = $this->actingAs($member)
        ->delete(route('tasks.comments.destroy', [$task, $comment]));
    $response->assertForbidden();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
    ]);
});

it('forbids an outsider from deleting the comment', function () {
    $owner = User::factory()->create();
    $commentOwner = User::factory()->create();
    $outsider = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($commentOwner);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);
    $comment = Comment::create([
        'task_id' => $task->id,
        'user_id' => $commentOwner->id,
        'body' => 'comment body',
    ]);

    $response = $this->actingAs($outsider)
        ->delete(route('tasks.comments.destroy', [$task, $comment]));
    $response->assertForbidden();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
    ]);
});

it('returns 404 when the comment does not belong to the given task', function () {
    $owner = User::factory()->create();
    $commentOwner = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($commentOwner);

    $taskOne = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $taskTwo = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $comment = Comment::create([
        'task_id' => $taskOne->id,
        'user_id' => $commentOwner->id,
        'body' => 'comment body',
    ]);

    $response = $this->actingAs($commentOwner)
        ->delete(route('tasks.comments.destroy', [$taskTwo, $comment]));

    $response->assertNotFound();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
    ]);
});

it('keeps the comment in the database after forbidden deletion attempt', function () {
    $owner = User::factory()->create();
    $commentOwner = User::factory()->create();
    $member = User::factory()->create();

    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $project->members()->attach([
        $commentOwner->id,
        $member->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $comment = Comment::create([
        'task_id' => $task->id,
        'user_id' => $commentOwner->id,
        'body' => 'Test comment',
    ]);

    $this
        ->actingAs($member)
        ->delete(route('tasks.comments.destroy', [$task, $comment]))
        ->assertForbidden();

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Test comment',
    ]);
});
