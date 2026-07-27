<?php

use App\Models\Project;
use App\Models\User;

it('allows the project owner to open the edit page', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $response = $this->actingAs($owner)
        ->get(route('projects.edit', $project));

    $response->assertOk();
    $response->assertViewIs('projects.edit');
    $response->assertViewHas('project', $project);
});

it('forbids a project member from opening the edit page', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $project->members()->attach($member);

    $response = $this
        ->actingAs($member)
        ->get(route('projects.edit', $project));

    $response->assertForbidden();
});

it('allows the project owner to update the project', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
        'name' => 'Ut labore corrupti.',
        'description' => 'Vero modi rerum dicta sit eos. Consequatur non veritatis labore. Voluptatem ipsa nostrum pariatur aliquid nulla autem consequuntur non.',
    ]);

    $response = $this->actingAs($owner)
        ->put(route('projects.update', $project), [
            'name' => 'Task Manager Project',
            'description' => 'Task Manager Description in details',
        ]);
    $response->assertRedirect(route('projects.index'));

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'owner_id' => $owner->id,
        'name' => 'Task Manager Project',
        'description' => 'Task Manager Description in details',
    ]);
});

it('forbids a project member from updating the project', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
        'name' => 'Task Manager Project',
    ]);

    $project->members()->attach($member);

    $response = $this->actingAs($member)
        ->put(route('projects.update', $project), [
            'name' => 'Unauthorized Project',
            'description' => 'Unauthorized Project',
        ]);
    $response->assertForbidden();

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
        'name' => 'Unauthorized Project',
    ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Task Manager Project',
    ]);

});

it('forbids an outsider from updating the project', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
        'name' => 'Task Manager Project',
    ]);

    $response = $this->actingAs($outsider)
        ->put(route('projects.update', $project), [
            'name' => 'Unauthorized Project from outsider',
            'description' => 'Unauthorized Project description from outsider',
        ]);
    $response->assertForbidden();
    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
        'name' => 'Unauthorized Project from outsider',
    ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Task Manager Project',
    ]);
});

it('allows the project owner to delete the project', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $response = $this->actingAs($owner)
        ->delete(route('projects.destroy', $project));

    $response->assertRedirect(route('projects.index'));

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

it('forbids a project member from deleting the project', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $project->members()->attach($member);

    $response = $this->actingAs($member)
        ->delete(route('projects.destroy', $project));

    $response->assertForbidden();
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
    ]);
});

it('forbids an outsider from deleting the project', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $response = $this->actingAs($outsider)
        ->delete(route('projects.destroy', $project));
    $response->assertForbidden();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
    ]);
});
