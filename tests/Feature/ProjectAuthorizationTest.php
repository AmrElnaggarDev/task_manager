<?php

use App\Models\Project;
use App\Models\User;

it('redirects guests to login when they try to view a project', function () {

    $project = Project::factory()->create();

    $response = $this->get(route('projects.show', $project));
    $response->assertRedirect(route('login'));
});

it('allows the project owner to view the project', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $response = $this->actingAs($owner)->get(route('projects.show', $project));

    $response->assertOk();
    $response->assertViewIs('projects.show');
    $response->assertViewHas('project', $project);

});

it('allows a project member to view the project', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $project->members()->attach($member);

    $response = $this->actingAs($member)->get(route('projects.show', $project));

    $response->assertOk();
    $response->assertViewIs('projects.show');
    $response->assertViewHas('project', $project);
});

it('forbids an outsider from viewing the project', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();

    $project = Project::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $response = $this
        ->actingAs($outsider)
        ->get(route('projects.show', $project));

    $response->assertForbidden();
});
