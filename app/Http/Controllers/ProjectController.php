<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = Auth::user()->ownedProjects()->withCount ([
            'tasks as to_do_tasks' => function ($query) {
            $query->where ('status', 'todo');
            },
            'tasks as in_progress' => function ($query) {
            $query->where ('status', 'in_progress');
            },
            'tasks as done' => function ($query) {
            $query->where ('status', 'done');
            }
        ])->get();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Project::class);

        return view ('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        // validate
        $attributes = $request->validate([
            'name' => 'required|string|max:255|unique:projects,name',
            'description' => 'nullable|string|',
        ]);

        $attributes['owner_id'] = Auth::id();
        Auth::user()->ownedProjects()->create($attributes);

        return redirect()->route('projects.index')->with('success', 'Project created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $teamMembers = $project->members()->get();
        $owner = $project->owner();
        return view('projects.show', compact('project', 'teamMembers', 'owner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    /**
     *
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $attributes = $request->validate([
            'name' => 'required|string|max:255|unique:projects,name,'.$project->id,
            'description' => 'nullable|string|',
        ]);

        $project->update($attributes);
        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
}
