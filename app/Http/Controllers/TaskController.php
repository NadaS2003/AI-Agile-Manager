<?php



namespace App\Http\Controllers;



use App\Models\Task;

use App\Models\Project;

use App\Models\Sprint;

use App\Support\UserNotifier;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;



class TaskController extends Controller

{

    public function index(Request $request)

    {

        $query = Task::where('user_id', $request->user()->id);



        if ($request->has('status') && in_array($request->status, ['pending', 'in_progress', 'completed'])) {

            $query->where('status', $request->status);

        }



        if ($request->query('today') == 1) {

            $query->whereDate('created_at', Carbon::today());

        }



        $tasks = $query->latest()->get();

        return view('tasks.index', compact('tasks'));

    }



    public function create(Request $request)

    {

        $projects = Project::where('user_id', $request->user()->id)->get();

        $sprints = collect();



        return view('tasks.create', compact('projects', 'sprints'));

    }



    public function store(Request $request)

    {

        $data = $request->validate([

            'title' => 'required|string|max:255',

            'description' => 'nullable|string',

            'due_date' => 'nullable|date|after_or_equal:now',

            'project_id' => 'nullable|exists:projects,id',

            'sprint_id' => 'nullable|exists:sprints,id',

            'priority' => 'nullable|in:low,medium,high,critical',

            'story_points' => 'nullable|integer|min:1|max:100',

        ]);



        $this->syncProjectAndSprint($request, $data);



        $data['user_id'] = $request->user()->id;

        $data['status'] = 'pending';

        $data['priority'] = $data['priority'] ?? 'medium';



        Task::create($data);



        if (! empty($data['project_id'])) {

            return redirect()->route('projects.backlog', $data['project_id'])

                ->with('success', 'Task added to backlog.');

        }



        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');

    }



    public function show(Request $request, int $id)

    {

        $task = Task::with(['project', 'sprint'])

            ->where('user_id', $request->user()->id)

            ->findOrFail($id);



        return view('tasks.show', compact('task'));

    }



    public function edit(Request $request, int $id)

    {

        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        $projects = Project::where('user_id', $request->user()->id)->get();

        $sprints = $task->project_id

            ? Sprint::where('project_id', $task->project_id)->get()

            : collect();



        return view('tasks.edit', compact('task', 'projects', 'sprints'));

    }



    public function update(Request $request, int $id)

    {

        $data = $request->validate([

            'title' => 'required|string|max:255',

            'description' => 'nullable|string',

            'due_date' => 'nullable|date|after_or_equal:now',

            'status' => 'required|in:pending,in_progress,completed',

            'project_id' => 'nullable|exists:projects,id',

            'sprint_id' => 'nullable|exists:sprints,id',

            'priority' => 'nullable|in:low,medium,high,critical',

            'story_points' => 'nullable|integer|min:1|max:100',

        ]);



        $this->syncProjectAndSprint($request, $data);



        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        if (array_key_exists('due_date', $data) && $task->due_date?->toDateTimeString() !== ($data['due_date'] ?? null)) {
            $data['due_soon_notified_at'] = null;
        }

        $task->update($data);



        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');

    }



    public function updateStatus(Request $request, int $id)

    {

        $data = $request->validate([

            'status' => 'required|in:pending,in_progress,completed',

        ]);



        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        $task->update($data);



        return redirect()->back()->with('success', 'Task status updated successfully.');

    }



    public function destroy(Request $request, int $id)

    {

        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        $task->delete();



        return redirect()->back()->with('success', 'Task deleted successfully.');

    }



    public function moveToSprint(Request $request, Task $task)

    {

        $this->authorizeTask($task);



        $data = $request->validate([

            'sprint_id' => 'required|exists:sprints,id',

        ]);



        $sprint = Sprint::with('project')->findOrFail($data['sprint_id']);

        abort_unless($sprint->project->user_id === $request->user()->id, 403);

        abort_unless($task->project_id === $sprint->project_id, 422);



        $task->update(['sprint_id' => $sprint->id]);



        return redirect()->back()->with('success', 'Task moved to sprint.');

    }



    public function updateKanban(Request $request, Task $task)

    {

        $this->authorizeTask($task);



        $data = $request->validate([

            'status' => 'required|in:pending,in_progress,completed',

            'sort_order' => 'nullable|integer',

        ]);



        $task->update($data);



        return response()->json(['success' => true, 'task' => $task]);

    }



    public function sprintsForProject(Project $project)

    {

        abort_unless($project->user_id === Auth::id(), 403);



        $sprints = $project->sprints()

            ->where('status', '!=', 'completed')

            ->get(['id', 'name', 'status']);



        return response()->json($sprints);

    }



    private function syncProjectAndSprint(Request $request, array &$data): void

    {

        if (! empty($data['project_id'])) {

            Project::where('user_id', $request->user()->id)->findOrFail($data['project_id']);

        }



        if (! empty($data['sprint_id'])) {

            $sprint = Sprint::with('project')->findOrFail($data['sprint_id']);

            abort_unless($sprint->project->user_id === $request->user()->id, 403);



            if (! empty($data['project_id'])) {

                abort_unless((int) $data['project_id'] === (int) $sprint->project_id, 422);

            }



            $data['project_id'] = $sprint->project_id;

        }

    }



    private function authorizeTask(Task $task): void

    {

        abort_unless($task->user_id === Auth::id(), 403);

    }

}

