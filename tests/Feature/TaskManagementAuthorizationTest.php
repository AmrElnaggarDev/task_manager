<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

it('allows the task creator to open the edit page', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
    ]);

    $response = $this->actingAs($creator)
        ->get(route('tasks.edit', $task));

    $response->assertOk();
    $response->assertViewIs('tasks.edit');
    $response->assertViewHas('task', $task);
    $response->assertViewHas('project', $project);
});

it('forbids the project owner from editing a task they did not create', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
    ]);

    $response = $this->actingAs($owner)
        ->get(route('tasks.edit', $task));

    $response->assertForbidden();
});

it('forbids a non-creator project member from opening the edit page', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $member = User::factory()->create();

    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach([$member->id, $creator->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
    ]);

    $response = $this->actingAs($member)
        ->get(route('tasks.edit', $task));
    $response->assertForbidden();

});

it('forbids an outsider from opening the task edit page', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();

    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($outsider)
        ->get(route('tasks.edit', $task));

    $response->assertForbidden();
});

it('allows the task creator to update the task', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'title' => 'Todo Task 1',
        'description' => 'TODO TASK1 DESCRIPTION',
        'status' => 'todo',
        'priority' => 'medium',
    ]);

    $response = $this
        ->actingAs($creator)
        ->put(route('tasks.update', $task), [
            'assigned_to' => null,
            'title' => 'TODO TASK 2',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'priority' => 'high',
            'deadline' => null,
        ]);
    $response->assertRedirect(route('tasks.index', $project));
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'title' => 'TODO TASK 2',
        'description' => 'Updated description',
        'status' => 'in_progress',
        'priority' => 'high',
    ]);

});

it('forbids a non-creator member from updating the task', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach([$member->id, $creator->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'title' => 'API handle',
    ]);

    $response = $this
        ->actingAs($member)
        ->put(route('tasks.update', $task), [
            'assigned_to' => null,
            'title' => 'Unauthorized Update',
            'description' => null,
            'status' => 'done',
            'priority' => 'high',
            'deadline' => null,
        ]);
    $response->assertForbidden();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'API handle',
    ]);
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
        'title' => 'Unauthorized Update',
    ]);
});

it('forbids a removed task creator from updating the task', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'title' => 'API handle',
    ]);
    $project->members()->detach($creator);

    $response = $this->actingAs($creator)
        ->put(route('tasks.update', $task), [
            'assigned_to' => null,
            'title' => 'Unauthorized Removed Creator Update',
            'description' => null,
            'status' => 'done',
            'priority' => 'high',
            'deadline' => null,
        ]);
    $response->assertForbidden();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'API handle',
    ]);

});

it('allows the task creator to delete the task', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
    ]);

    $response = $this->actingAs($creator)
        ->delete(route('tasks.destroy', $task));
    $response->assertRedirect(route('tasks.index', $project));
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,

    ]);
});

it('forbids a non-creator member from deleting the task', function () {
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
    ]);

    $response = $this->actingAs($member)
        ->delete(route('tasks.destroy', $task));

    $response->assertForbidden();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
    ]);
});

it('forbids a removed task creator from deleting the task', function () {
    $owner = User::factory()->create();
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($creator);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
    ]);
    $project->members()->detach($creator);

    $response = $this->actingAs($creator)
        ->delete(route('tasks.destroy', $task));

    $response->assertForbidden();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
    ]);
});
