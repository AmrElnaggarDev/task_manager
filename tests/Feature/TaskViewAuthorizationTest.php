<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

it('redirects guests to login when they try to view a task', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $owner->id,
    ]);

    $response = $this->get(route('tasks.show', $task));
    $response->assertRedirect(route('login'));
});

it('allows the project owner to view a task', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $owner->id,
    ]);

    $response = $this->actingAs($owner)->get(route('tasks.show', $task));

    $response->assertOk();
    $response->assertViewIs('tasks.show');
    $response->assertViewHas('task', $task);
    $response->assertViewHas('project', $project);
});

it('allows a project member to view a task', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $project->members()->attach($member);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $owner->id,
    ]);

    $response = $this->actingAs($member)->get(route('tasks.show', $task));
    $response->assertOk();
    $response->assertViewIs('tasks.show');
    $response->assertViewHas('task', $task);
    $response->assertViewHas('project', $project);

});

it('forbids an outsider from viewing a task', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $owner->id,
    ]);

    $response = $this->actingAs($outsider)->get(route('tasks.show', $task));
    $response->assertForbidden();
});

it('forbids a removed project member from viewing a task', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $project->members()->attach($member);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $owner->id,
    ]);
    $project->members()->detach($member);

    $response = $this->actingAs($member)->get(route('tasks.show', $task));
    $response->assertForbidden();
});
