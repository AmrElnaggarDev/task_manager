<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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

        $today = Carbon::today()->toDateString();
        $sevenDaysFromNow = Carbon::now()->addDays(7)->toDateString();

        $overDueTasks = $user->createdTasks()
            ->with('project')
            ->where ('status', '!=', 'done')
            ->where('deadline', '<', $today)
            ->whereNotNull('deadline')
            ->orderBy('deadline')
            ->take(5)
            ->get();

        $dueTodayTasks = $user->createdTasks()
            ->with('project')
            ->where ('status', '!=', 'done')
            ->whereDate('deadline', '=', $today)
            ->orderBy('deadline' )
            ->take(5)->get();

        $upcomingTasks = $user->createdTasks()
            ->with('project')
            ->where('status', '!=', 'done')
            ->where('deadline', '>', $today)
            ->where ('deadline', '<=', $sevenDaysFromNow)
            ->orderBy('deadline')
            ->take(5)
            ->get();

        return view('dashboard', compact('user', 'projectsCount', 'tasksCount', 'todoTasksCount',
              'inProgressTasksCount', 'doneTasksCount', 'latestProjects', 'latestTasks', 'overDueTasks', 'dueTodayTasks', 'upcomingTasks'));


    }
}
