<?php



namespace App\Http\Controllers;



use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Support\UserNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;



class ProjectController extends Controller

{

    public function index(Request $request)

    {

        $projects = Project::where('user_id', $request->user()->id)

            ->withCount(['tasks', 'sprints'])

            ->latest()

            ->get();



        return view('projects.index', compact('projects'));

    }



    public function create()

    {

        return view('projects.create');

    }



    public function store(Request $request)

    {

        $data = $request->validate([

            'name' => 'required|string|max:255',

            'description' => 'nullable|string',

            'color' => 'nullable|string|max:7',

            'status' => 'required|in:planning,active,on_hold,completed,archived',

        ]);



        $data['user_id'] = $request->user()->id;

        $data['color'] = $data['color'] ?? '#3525cd';



        $project = Project::create($data);



        return redirect()->route('projects.show', $project)

            ->with('success', 'Project created successfully!');

    }



    public function show(Request $request, Project $project)

    {
        $this->authorizeProject($request, $project);



        $project->load(['sprints', 'tasks']);

        $activeSprint = $project->activeSprint();

        $backlogCount = $project->backlogTasks()->count();

        $sprints = $project->sprints()->with('tasks')->get();



        $totalTasks = $project->tasks->count();

        $completedTasks = $project->tasks->where('status', 'completed')->count();

        $totalPoints = $project->tasks->sum('story_points');



        return view('projects.show', compact(

            'project', 'activeSprint', 'backlogCount', 'sprints',

            'totalTasks', 'completedTasks', 'totalPoints'

        ));

    }



    public function edit(Request $request, Project $project)

    {
        $this->authorizeProject($request, $project);



        return view('projects.edit', compact('project'));

    }



    public function update(Request $request, Project $project)

    {
        $this->authorizeProject($request, $project);



        $data = $request->validate([

            'name' => 'required|string|max:255',

            'description' => 'nullable|string',

            'color' => 'nullable|string|max:7',

            'status' => 'required|in:planning,active,on_hold,completed,archived',

        ]);



        $project->update($data);



        return redirect()->route('projects.show', $project)

            ->with('success', 'Project updated successfully!');

    }



    public function destroy(Request $request, Project $project)
    {
        $this->authorizeProject($request, $project);

        $project->delete();



        return redirect()->route('projects.index')

            ->with('success', 'Project deleted successfully.');

    }



    public function backlog(Request $request, Project $project)
    {
        $this->authorizeProject($request, $project);



        $backlogTasks = $project->tasks()

            ->whereNull('sprint_id')

            ->orderByRaw("FIELD(priority, 'critical','high','medium','low')")

            ->orderBy('sort_order')

            ->get();



        $sprints = $project->sprints()->where('status', '!=', 'completed')->get();



        return view('projects.backlog', compact('project', 'backlogTasks', 'sprints'));

    }



    public function generateAi(Request $request)
    {
        $request->validate([
            'idea' => 'required|string|max:5000',
        ]);

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gemini API key is not configured. Please set GEMINI_API_KEY in your .env file.');
        }

        $idea = $request->input('idea');

        $prompt = "Act as a Senior Full-Stack Software Engineer and Agile Scrum Master.
Generate a structured, production-ready Agile project plan based on the user's project concept/idea.

RULES & CONSTRAINTS:
1. STRICT JSON OUTPUT ONLY: The output MUST be strictly valid JSON without any markdown formatting wrappers (no ```json, no surrounding prose).
2. SPRINT BREAKDOWN: Divide the project into 2 to 4 logical, time-boxed Sprints depending on project scope, with clear Sprint Goals.
3. STORY POINTS: Assign Fibonacci story points (1, 2, 3, 5, 8, 13) based on task complexity.
4. PRIORITIES: Strictly use one of these values: \"Low\", \"Medium\", \"High\", \"Critical\".
5. STATUSES: Strictly use one of these values: \"Backlog\", \"To Do\", \"In Progress\", \"Done\" (default newly generated tasks to \"Backlog\" or \"To Do\").
6. USER STORIES: Generate realistic titles and descriptions with basic acceptance criteria.

EXPECTED JSON SCHEMA OUTPUT:
{
  \"project\": {
    \"name\": \"Short, clear project title\",
    \"description\": \"Comprehensive summary of the project scope and primary objectives.\"
  },
  \"sprints\": [
    {
      \"name\": \"Sprint 1: Core Foundation & Setup\",
      \"goal\": \"Establish primary architecture, database schemas, and user authentication.\",
      \"duration_weeks\": 2,
      \"tasks\": [
        {
          \"title\": \"User Story / Task Title\",
          \"description\": \"Task details and acceptance criteria\",
          \"story_points\": 5,
          \"priority\": \"High\",
          \"status\": \"To Do\"
        }
      ]
    }
  ]
}

USER INPUT IDEA:
" . json_encode($idea);

        try {
         //   dd(config('services.gemini.key'));
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->failed()) {
                dd(
                    $response->status(),
                    $response->headers(),
                    $response->body()
                );
               // throw new \Exception("Gemini API request failed: " . $response->body());
            }

            $resData = $response->json();
            $jsonText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($jsonText)) {
                throw new \Exception("AI returned an empty response. Please try with a more detailed description.");
            }

            // Strip any potential markdown code blocks wrapper
            $jsonText = preg_replace('/^```(?:json)?|```$/m', '', trim($jsonText));

            $projectPlan = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("AI returned invalid JSON: " . json_last_error_msg());
            }

            if (empty($projectPlan['project']['name'])) {
                throw new \Exception("AI failed to generate a valid project structure.");
            }

            // pre-defined colors
            $colors = ['#3525cd','#e11d48','#16a34a','#d97706','#7c3aed','#0891b2','#be185d','#374151'];
            $color = $colors[array_rand($colors)];

            // Create Project
            $project = Project::create([
                'user_id' => $request->user()->id,
                'name' => $projectPlan['project']['name'],
                'description' => $projectPlan['project']['description'] ?? '',
                'color' => $color,
                'status' => 'active', // default to active status
            ]);

            $sprintsData = $projectPlan['sprints'] ?? [];
            $currentDate = now()->startOfDay();

            foreach ($sprintsData as $index => $sprintData) {
                $durationWeeks = $sprintData['duration_weeks'] ?? 2;
                $startDate = $currentDate->copy();
                $endDate = $startDate->copy()->addWeeks($durationWeeks)->subDay();

                $currentDate = $endDate->copy()->addDay();
                $sprintStatus = ($index === 0) ? 'active' : 'planning';

                $sprint = Sprint::create([
                    'project_id' => $project->id,
                    'name' => $sprintData['name'] ?? ('Sprint ' . ($index + 1)),
                    'goal' => $sprintData['goal'] ?? null,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $sprintStatus,
                ]);

                $tasksData = $sprintData['tasks'] ?? [];
                foreach ($tasksData as $taskIndex => $taskData) {
                    $priority = strtolower($taskData['priority'] ?? 'medium');
                    if (!in_array($priority, ['low', 'medium', 'high', 'critical'])) {
                        $priority = 'medium';
                    }

                    $storyPoints = $taskData['story_points'] ?? null;
                    $aiStatus = strtolower(trim($taskData['status'] ?? 'to do'));

                    $sprintId = $sprint->id;
                    $dbStatus = 'pending';

                    if ($aiStatus === 'backlog') {
                        $sprintId = null;
                        $dbStatus = 'pending';
                    } elseif (in_array($aiStatus, ['to do', 'todo', 'pending'])) {
                        $dbStatus = 'pending';
                    } elseif (in_array($aiStatus, ['in progress', 'in_progress'])) {
                        $dbStatus = 'in_progress';
                    } elseif (in_array($aiStatus, ['done', 'completed'])) {
                        $dbStatus = 'completed';
                    }

                    Task::create([
                        'user_id' => $request->user()->id,
                        'project_id' => $project->id,
                        'sprint_id' => $sprintId,
                        'title' => $taskData['title'] ?? 'AI Story',
                        'description' => $taskData['description'] ?? null,
                        'priority' => $priority,
                        'story_points' => $storyPoints,
                        'status' => $dbStatus,
                        'sort_order' => $taskIndex,
                    ]);
                }
            }

            return redirect()->route('projects.show', $project)
                ->with('success', 'AI Project Plan successfully generated and populated!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'AI Generation Error: ' . $e->getMessage());
        }
    }



    private function authorizeProject(Request $request, Project $project): void
    {
        abort_unless($project->user_id === $request->user()?->id, 403);
    }

}
