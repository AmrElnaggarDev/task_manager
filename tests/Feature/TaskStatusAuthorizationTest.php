<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

it('allows the task creator to update the task status', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'status' => 'todo',
    ]);

    $response = $this->actingAs($creator)
        ->patch(route('tasks.status.update', $task), [
            'status' => 'done',
        ]);
    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'done',
    ]);
});

it('allows the assigned user to update the task status', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $assignee = User::factory()->create();

    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach([$assignee->id, $creator->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'assigned_to' => $assignee->id,
        'status' => 'todo',
    ]);

    $response = $this->actingAs($assignee)
        ->patch(route('tasks.status.update', $task), [
            'status' => 'done',
        ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'done',
    ]);
});

it('forbids the project owner from updating task status if not creator or assignee', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'status' => 'todo',
    ]);

    $response = $this->actingAs($owner)
        ->patch(route('tasks.status.update', $task), [
            'status' => 'done',
        ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
        'status' => 'done',
    ]);
});

it('forbids a project member who is not creator or assignee from updating task status', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach([$creator->id, $member->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'status' => 'todo',
    ]);

    $response = $this->actingAs($member)
        ->patch(route('tasks.status.update', $task), [
            'status' => 'done',
        ]);
    $response->assertForbidden();
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
        'status' => 'done',
    ]);

});

it('forbids an outsider from updating task status', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $outsider = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'status' => 'todo',
    ]);

    $response = $this->actingAs($outsider)
        ->patch(route('tasks.status.update', $task), [
            'status' => 'done',
        ]);
    $response->assertForbidden();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'todo',
    ]);

});

it('forbids a removed creator from updating task status', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'status' => 'todo',
    ]);
    $project->members()->detach($creator);

    $response = $this->actingAs($creator)
        ->patch(route('tasks.status.update', $task), [
            'status' => 'done',
        ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'todo',
    ]);
});

it('forbids a removed assignee from updating task status', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $assignee = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $project->members()->attach([$assignee->id, $creator->id]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'assigned_to' => $assignee->id,
        'status' => 'todo',
    ]);

    $project->members()->detach($assignee);

    $response = $this->actingAs($assignee)
        ->patch(route('tasks.status.update', $task), [
            'status' => 'done',
        ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'todo',
    ]);
});
