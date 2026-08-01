<x-layouts.app>
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-sm text-sm text-secondary mb-lg">
        <a href="{{ route('projects.index') }}" class="hover:text-primary">Projects</a>
        <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        <a href="{{ route('projects.show', $project) }}" class="hover:text-primary">{{ $project->name }}</a>
        <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        <span class="text-on-surface font-medium">Sprints</span>
    </div>

    <div class="flex items-center justify-between mb-xl">
        <h1 class="text-2xl font-bold text-on-surface">Sprints</h1>
        <a href="{{ route('projects.sprints.create', $project) }}"
           class="flex items-center gap-xs px-md py-sm bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-all">
            <span class="material-symbols-outlined" style="font-size:16px">add</span>
            New Sprint
        </a>
    </div>

    @if($sprints->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <span class="material-symbols-outlined text-secondary mb-md" style="font-size:48px">sprint</span>
            <h2 class="font-bold text-on-surface mb-sm">No sprints yet</h2>
            <p class="text-secondary text-sm mb-lg">Create your first sprint to start tracking work in time-boxes.</p>
            <a href="{{ route('projects.sprints.create', $project) }}"
               class="flex items-center gap-xs px-lg py-sm bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-all">
                <span class="material-symbols-outlined" style="font-size:16px">add</span>
                Create Sprint
            </a>
        </div>
    @else
        <div class="flex flex-col gap-md">
            @foreach($sprints as $sprint)
                @php
                    $total    = $sprint->tasks->sum('story_points');
                    $done     = $sprint->tasks->where('status','completed')->sum('story_points');
                    $pct      = $total > 0 ? round(($done/$total)*100) : 0;
                @endphp
                <div class="bg-white rounded-2xl border border-outline-variant p-lg hover:shadow-sm transition-all">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-sm mb-xs">
                                <h2 class="font-bold text-on-surface">{{ $sprint->name }}</h2>
                                <span class="badge {{ $sprint->statusColor }}">{{ $sprint->statusLabel }}</span>
                            </div>
                            @if($sprint->goal)
                                <p class="text-secondary text-sm mb-md">{{ $sprint->goal }}</p>
                            @endif

                            {{-- Progress --}}
                            <div class="mb-md max-w-sm">
                                <div class="flex justify-between text-xs text-secondary mb-xs">
                                    <span>Progress</span>
                                    <span>{{ $done }}/{{ $total }} pts ({{ $pct }}%)</span>
                                </div>
                                <div class="h-1.5 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full progress-bar" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>

                            <div class="flex items-center gap-md text-xs text-secondary">
                                <span class="flex items-center gap-xs">
                                    <span class="material-symbols-outlined" style="font-size:14px">task_alt</span>
                                    {{ $sprint->tasks_count }} tasks
                                </span>
                                @if($sprint->start_date)
                                    <span class="flex items-center gap-xs">
                                        <span class="material-symbols-outlined" style="font-size:14px">event</span>
                                        {{ $sprint->start_date->format('M d') }} — {{ $sprint->end_date?->format('M d, Y') ?? 'No end' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-sm ml-lg flex-shrink-0">
                            <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}"
                               class="flex items-center gap-xs px-md py-sm bg-surface-container-low border border-outline-variant rounded-xl text-sm font-semibold text-secondary hover:text-primary hover:border-primary transition-all">
                                <span class="material-symbols-outlined" style="font-size:16px">view_kanban</span>
                                Board
                            </a>

                            @if($sprint->status === 'planning')
                                <form action="{{ route('projects.sprints.start', [$project, $sprint]) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-xs px-md py-sm bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition-all">
                                        <span class="material-symbols-outlined" style="font-size:16px">play_arrow</span>
                                        Start
                                    </button>
                                </form>
                            @elseif($sprint->status === 'active')
                                <form action="{{ route('projects.sprints.complete', [$project, $sprint]) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-xs px-md py-sm bg-purple-600 text-white rounded-xl text-sm font-semibold hover:bg-purple-700 transition-all">
                                        <span class="material-symbols-outlined" style="font-size:16px">flag</span>
                                        Complete
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('projects.sprints.edit', [$project, $sprint]) }}"
                               class="w-9 h-9 flex items-center justify-center border border-outline-variant rounded-xl text-secondary hover:text-amber-600 hover:bg-amber-50 transition-all">
                                <span class="material-symbols-outlined" style="font-size:18px">edit</span>
                            </a>

                            <form id="delete-sprint-{{ $sprint->id }}" action="{{ route('projects.sprints.destroy', [$project, $sprint]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete(event, 'delete-sprint-{{ $sprint->id }}')"
                                        class="w-9 h-9 flex items-center justify-center border border-outline-variant rounded-xl text-secondary hover:text-error hover:bg-error/5 transition-all">
                                    <span class="material-symbols-outlined" style="font-size:18px">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
