<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (Str::length($query) < 2) {
            return response()->json([
                'query' => $query,
                'total' => 0,
                'results' => [
                    'tasks' => [],
                    'projects' => [],
                    'sprints' => [],
                ],
            ]);
        }

        $like = '%'.$query.'%';
        $userId = $request->user()->id;

        $tasks = Task::query()
            ->where('user_id', $userId)
            ->where(function ($taskQuery) use ($like) {
                $taskQuery->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('status', 'like', $like)
                    ->orWhere('priority', 'like', $like);
            })
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'type' => 'task',
                'title' => $task->title,
                'subtitle' => trim(ucwords(str_replace('_', ' ', $task->status)).' task'),
                'url' => route('tasks.show', $task),
            ]);

        $projects = Project::query()
            ->where('user_id', $userId)
            ->where(function ($projectQuery) use ($like) {
                $projectQuery->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('status', 'like', $like);
            })
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'type' => 'project',
                'title' => $project->name,
                'subtitle' => $project->statusLabel.' project',
                'url' => route('projects.show', $project),
            ]);

        $sprints = Sprint::query()
            ->whereHas('project', fn ($projectQuery) => $projectQuery->where('user_id', $userId))
            ->where(function ($sprintQuery) use ($like) {
                $sprintQuery->where('name', 'like', $like)
                    ->orWhere('goal', 'like', $like)
                    ->orWhere('status', 'like', $like);
            })
            ->with('project')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Sprint $sprint) => [
                'id' => $sprint->id,
                'type' => 'sprint',
                'title' => $sprint->name,
                'subtitle' => $sprint->project->name.' sprint',
                'url' => route('projects.sprints.show', [$sprint->project, $sprint]),
            ]);

        return response()->json([
            'query' => $query,
            'total' => $tasks->count() + $projects->count() + $sprints->count(),
            'results' => [
                'tasks' => $tasks,
                'projects' => $projects,
                'sprints' => $sprints,
            ],
        ]);
    }
}
