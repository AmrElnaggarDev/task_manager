<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $projectsCount = $user->ownedProjects()->count();
        $tasksCount = $user->createdTasks()->count();
        $todoTasksCount = $user->createdTasks()
            ->where ('status', 'todo')
            ->count();
        $inProgressTasksCount = $user->createdTasks()
            ->where ('status', 'in_progress')
            ->count();
        $doneTasksCount = $user->createdTasks()
            ->where ('status', 'done')
            ->count();
        $latestProjects = $user->ownedProjects()
            ->latest()
            ->take(5)
            ->get();
        $latestTasks = $user->createdTasks()
            ->latest()
            ->with ('project')
            ->take(5)
            ->get();

        return view('dashboard', compact('user', 'projectsCount', 'tasksCount', 'todoTasksCount',
              'inProgressTasksCount', 'doneTasksCount', 'latestProjects', 'latestTasks'));


    }
}
