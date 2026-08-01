<x-layouts.app>
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-sm text-sm text-secondary mb-lg">
        <a href="{{ route('projects.index') }}" class="hover:text-primary transition-colors">Projects</a>
        <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        <span class="text-on-surface font-medium">{{ $project->name }}</span>
    </div>

    {{-- Project Header --}}
    <div class="flex items-start justify-between mb-xl">
        <div class="flex items-center gap-md">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-md"
                 style="background: {{ $project->color }}">
                {{ strtoupper(substr($project->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-sm">
                    <h1 class="text-2xl font-bold text-on-surface">{{ $project->name }}</h1>
                    <span class="badge {{ $project->statusColor }}">{{ $project->statusLabel }}</span>
                </div>
                @if($project->description)
                    <p class="text-secondary text-sm mt-xs max-w-xl">{{ $project->description }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-sm">
            <a href="{{ route('projects.backlog', $project) }}"
               class="flex items-center gap-xs px-md py-sm border border-outline-variant rounded-xl text-sm font-semibold text-secondary hover:bg-gray-50 transition-all">
                <span class="material-symbols-outlined" style="font-size:16px">list</span>
                Backlog
            </a>
            <a href="{{ route('projects.sprints.create', $project) }}"
               class="flex items-center gap-xs px-md py-sm bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-all">
                <span class="material-symbols-outlined" style="font-size:16px">add</span>
                New Sprint
            </a>
            <a href="{{ route('projects.edit', $project) }}"
               class="w-9 h-9 flex items-center justify-center border border-outline-variant rounded-xl text-secondary hover:text-amber-600 hover:bg-amber-50 transition-all">
                <span class="material-symbols-outlined" style="font-size:18px">settings</span>
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-md mb-xl">
        @php
            $statCards = [
                ['icon' => 'task_alt',      'label' => 'Total Tasks',      'value' => $totalTasks,     'color' => 'text-blue-600',   'bg' => 'bg-blue-50'],
                ['icon' => 'check_circle',  'label' => 'Completed',        'value' => $completedTasks, 'color' => 'text-green-600',  'bg' => 'bg-green-50'],
                ['icon' => 'sprint',        'label' => 'Sprints',          'value' => $sprints->count(),'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                ['icon' => 'star',          'label' => 'Story Points',     'value' => $totalPoints,    'color' => 'text-amber-600',  'bg' => 'bg-amber-50'],
            ];
        @endphp
        @foreach($statCards as $stat)
            <div class="bg-white rounded-2xl border border-outline-variant p-md flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl {{ $stat['bg'] }} flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined {{ $stat['color'] }}" style="font-size:20px">{{ $stat['icon'] }}</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-on-surface">{{ $stat['value'] }}</p>
                    <p class="text-xs text-secondary">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">

        {{-- Active Sprint (2/3 width) --}}
        <div class="lg:col-span-2 flex flex-col gap-md">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-on-surface flex items-center gap-sm">
                    <span class="material-symbols-outlined text-primary" style="font-size:20px">sprint</span>
                    Active Sprint
                </h2>
                <a href="{{ route('projects.sprints.index', $project) }}" class="text-sm text-primary hover:underline">All Sprints →</a>
            </div>

            @if($activeSprint)
                @php
                    $sp = $activeSprint;
                    $spTotal = $sp->tasks->sum('story_points');
                    $spDone  = $sp->tasks->where('status','completed')->sum('story_points');
                    $spPct   = $spTotal > 0 ? round(($spDone/$spTotal)*100) : 0;
                @endphp
                <div class="bg-white rounded-2xl border border-outline-variant p-lg">
                    <div class="flex items-center justify-between mb-md">
                        <div>
                            <h3 class="font-semibold text-on-surface">{{ $activeSprint->name }}</h3>
                            @if($activeSprint->goal)
                                <p class="text-secondary text-sm mt-xs">{{ $activeSprint->goal }}</p>
                            @endif
                        </div>
                        <a href="{{ route('projects.sprints.show', [$project, $activeSprint]) }}"
                           class="flex items-center gap-xs px-md py-xs bg-primary/10 text-primary rounded-lg text-sm font-semibold hover:bg-primary hover:text-white transition-all">
                            <span class="material-symbols-outlined" style="font-size:16px">view_kanban</span>
                            Kanban Board
                        </a>
                    </div>

                    {{-- Progress --}}
                    <div class="mb-md">
                        <div class="flex items-center justify-between text-xs text-secondary mb-xs">
                            <span>Sprint Progress</span>
                            <span class="font-semibold text-on-surface">{{ $spDone }} / {{ $spTotal }} pts ({{ $spPct }}%)</span>
                        </div>
                        <div class="h-2 bg-surface-variant rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full progress-bar" style="width: {{ $spPct }}%"></div>
                        </div>
                    </div>

                    {{-- Task counts --}}
                    <div class="grid grid-cols-3 gap-sm">
                        @php
                            $pending    = $activeSprint->tasks->where('status', 'pending')->count();
                            $inProgress = $activeSprint->tasks->where('status', 'in_progress')->count();
                            $done       = $activeSprint->tasks->where('status', 'completed')->count();
                        @endphp
                        <div class="bg-surface-container-low rounded-xl p-sm text-center">
                            <p class="text-lg font-bold text-on-surface">{{ $pending }}</p>
                            <p class="text-xs text-secondary">To Do</p>
                        </div>
                        <div class="bg-primary-container rounded-xl p-sm text-center">
                            <p class="text-lg font-bold text-on-surface">{{ $inProgress }}</p>
                            <p class="text-xs text-secondary">In Progress</p>
                        </div>
                        <div class="bg-tertiary-fixed/30 rounded-xl p-sm text-center">
                            <p class="text-lg font-bold text-on-surface">{{ $done }}</p>
                            <p class="text-xs text-secondary">Done</p>
                        </div>
                    </div>

                    @if($activeSprint->end_date)
                        <div class="mt-md pt-md border-t border-outline-variant/50 flex items-center gap-xs text-xs text-secondary">
                            <span class="material-symbols-outlined" style="font-size:14px">schedule</span>
                            {{ $activeSprint->daysLeft }} days left
                            ({{ $activeSprint->start_date?->format('M d') }} — {{ $activeSprint->end_date->format('M d, Y') }})
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-2xl border-2 border-dashed border-outline-variant p-xl flex flex-col items-center text-center gap-sm">
                    <span class="material-symbols-outlined text-secondary" style="font-size:36px">sprint</span>
                    <p class="font-semibold text-on-surface">No active sprint</p>
                    <p class="text-sm text-secondary">Create a sprint and start it to track progress</p>
                    <a href="{{ route('projects.sprints.create', $project) }}"
                       class="flex items-center gap-xs px-md py-sm bg-primary text-white rounded-xl text-sm font-semibold mt-sm hover:bg-primary-dark transition-all">
                        <span class="material-symbols-outlined" style="font-size:16px">add</span>
                        Create Sprint
                    </a>
                </div>
            @endif
        </div>

        {{-- Sprint list sidebar (1/3) --}}
        <div class="flex flex-col gap-md">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-on-surface">All Sprints</h2>
                <a href="{{ route('projects.sprints.create', $project) }}"
                   class="text-primary text-sm hover:underline flex items-center gap-xs">
                    <span class="material-symbols-outlined" style="font-size:14px">add</span> New
                </a>
            </div>

            @forelse($sprints as $sprint)
                <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}"
                   class="bg-white rounded-xl border border-outline-variant p-md hover:shadow-sm hover:border-primary/30 transition-all group">
                    <div class="flex items-center justify-between mb-xs">
                        <span class="font-semibold text-sm text-on-surface group-hover:text-primary transition-colors">{{ $sprint->name }}</span>
                        <span class="badge {{ $sprint->statusColor }}">{{ $sprint->statusLabel }}</span>
                    </div>
                    @if($sprint->goal)
                        <p class="text-xs text-secondary line-clamp-1">{{ $sprint->goal }}</p>
                    @endif
                    <div class="flex items-center gap-sm mt-sm text-xs text-secondary">
                        <span>{{ $sprint->tasks->count() }} tasks</span>
                        <span>·</span>
                        <span>{{ $sprint->tasks->sum('story_points') }} pts</span>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-xl border border-outline-variant p-md text-center text-secondary text-sm">
                    No sprints yet
                </div>
            @endforelse

            {{-- Backlog shortcut --}}
            <a href="{{ route('projects.backlog', $project) }}"
               class="bg-white rounded-xl border border-outline-variant p-md flex items-center justify-between hover:shadow-sm hover:border-primary/30 transition-all group">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-secondary" style="font-size:18px">list</span>
                    <span class="font-semibold text-sm text-on-surface group-hover:text-primary transition-colors">Product Backlog</span>
                </div>
                <span class="badge bg-surface-variant text-on-surface-variant">{{ $backlogCount }}</span>
            </a>
        </div>
    </div>
</x-layouts.app>
