<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    public function store(Project $project)
    {
        $this->authorize('update', $project);

        request()->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', request('email'))->first();

        if ($user->id === $project->owner_id) {
            return back()->withErrors([
                'email' => 'Project owner cannot be added as a member.',
            ]);
        }

        if ($project->members()->where('user_id', $user->id)->exists()) {
            return back()->withErrors([
                'email' => 'This user is already a member of this project.',
            ]);
        }

        $project->members()->attach($user->id);

        Activity::log($project, auth()->user(), 'member_added', "added {$user->name} to the project");

        return back()->with('success', 'Member added successfully.');
    }

    public function destroy(Project $project, User $user)
    {
        $this->authorize('update', $project);

        if ($user->id === $project->owner_id) {
            return back()->withErrors([
                'member' => 'Project owner cannot be removed.',
            ]);
        }

        $project->members()->detach($user->id);

        Activity::log($project, auth()->user(), 'member_removed', "removed {$user->name} from the project");

        return back()->with('success', 'Member removed successfully.');
    }

}
