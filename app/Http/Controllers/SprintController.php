<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Support\UserNotifier;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function index(Project $project)
    {
        $this->authorizeProject($project);

        $sprints = $project->sprints()->withCount('tasks')->with('tasks')->get();
        return view('sprints.index', compact('project', 'sprints'));
    }

    public function create(Project $project)
    {
        $this->authorizeProject($project);

        return view('sprints.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['project_id'] = $project->id;
        $data['status'] = 'planning';

        $sprint = Sprint::create($data);

        return redirect()->route('projects.sprints.show', [$project, $sprint])
            ->with('success', 'Sprint created! Add tasks from the backlog.');
    }

    public function show(Project $project, Sprint $sprint)
    {
        $this->authorizeSprint($project, $sprint);

        $sprint->load(['tasks' => function ($q) {
            $q->orderBy('sort_order');
        }]);

        $todoTasks = $sprint->tasks->where('status', 'pending');
        $inProgressTasks = $sprint->tasks->where('status', 'in_progress');
        $doneTasks = $sprint->tasks->where('status', 'completed');

        $totalPoints = $sprint->tasks->sum('story_points');
        $completedPoints = $sprint->tasks->where('status', 'completed')->sum('story_points');
        $progress = $totalPoints > 0 ? round(($completedPoints / $totalPoints) * 100) : 0;

        return view('sprints.show', compact(
            'project', 'sprint',
            'todoTasks', 'inProgressTasks', 'doneTasks',
            'totalPoints', 'completedPoints', 'progress'
        ));
    }

    public function edit(Project $project, Sprint $sprint)
    {
        $this->authorizeSprint($project, $sprint);

        return view('sprints.edit', compact('project', 'sprint'));
    }

    public function update(Request $request, Project $project, Sprint $sprint)
    {
        $this->authorizeSprint($project, $sprint);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:planning,active,completed',
        ]);

        $sprint->update($data);

        return redirect()->route('projects.sprints.show', [$project, $sprint])
            ->with('success', 'Sprint updated successfully.');
    }

    public function destroy(Project $project, Sprint $sprint)
    {
        $this->authorizeSprint($project, $sprint);

        $sprint->tasks()->update(['sprint_id' => null]);
        $sprint->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Sprint deleted. Tasks moved back to backlog.');
    }

    public function start(Request $request, Project $project, Sprint $sprint)
    {
        $this->authorizeSprint($project, $sprint);

        $project->sprints()->where('status', 'active')->update(['status' => 'planning']);
        $sprint->update(['status' => 'active']);

        return redirect()->route('projects.sprints.show', [$project, $sprint])
            ->with('success', 'Sprint started!');
    }

    public function complete(Project $project, Sprint $sprint)
    {
        $this->authorizeSprint($project, $sprint);

        $sprint->update(['status' => 'completed']);
        $sprint->tasks()->where('status', '!=', 'completed')->update(['sprint_id' => null]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Sprint completed! Incomplete tasks moved back to backlog.');
    }

    private function authorizeProject(Project $project): void
    {
        abort_unless($project->user_id === auth()->id(), 403);
    }

    private function authorizeSprint(Project $project, Sprint $sprint): void
    {
        $this->authorizeProject($project);
        abort_unless($sprint->project_id === $project->id, 404);
    }
}
