<x-layouts.app>
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-xl">
        <div>
            <h1 class="text-2xl font-bold text-on-surface">Projects</h1>
            <p class="text-secondary text-sm mt-xs">Manage your agile projects and sprints</p>
        </div>
        <a href="{{ route('projects.create') }}"
           class="flex items-center gap-sm px-lg py-sm bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-dark active:scale-95 transition-all shadow-sm">
            <span class="material-symbols-outlined text-base">add</span>
            New Project
        </a>
    </div>

    @if($projects->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center mb-lg">
                <span class="material-symbols-outlined text-primary" style="font-size:40px">rocket_launch</span>
            </div>
            <h2 class="text-xl font-bold text-on-surface mb-sm">No projects yet</h2>
            <p class="text-secondary max-w-sm mb-lg">Create your first project to start organizing tasks with sprints and a Kanban board.</p>
            <a href="{{ route('projects.create') }}"
               class="flex items-center gap-sm px-lg py-sm bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-dark transition-all">
                <span class="material-symbols-outlined text-base">add</span>
                Create First Project
            </a>
        </div>
    @else
        {{-- Project grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-md">
            @foreach($projects as $project)
                @php
                    $activeSprint = $project->sprints()->where('status', 'active')->first();
                @endphp
                <div class="bg-white rounded-2xl border border-outline-variant p-lg flex flex-col gap-md hover:shadow-md hover:-translate-y-1 transition-all duration-200 cursor-pointer group"
                     onclick="window.location='{{ route('projects.show', $project) }}'">

                    {{-- Header --}}
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-sm">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                                 style="background: {{ $project->color }}">
                                {{ strtoupper(substr($project->name, 0, 1)) }}
                            </div>
                            <div>
                                <h2 class="font-semibold text-on-surface group-hover:text-primary transition-colors">{{ $project->name }}</h2>
                                <span class="badge {{ $project->statusColor }}">{{ $project->statusLabel }}</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-xs opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">
                            <a href="{{ route('projects.edit', $project) }}"
                               class="w-7 h-7 flex items-center justify-center rounded-full text-secondary hover:text-amber-600 hover:bg-amber-50 transition-all">
                                <span class="material-symbols-outlined" style="font-size:16px">edit</span>
                            </a>
                            <form id="delete-project-{{ $project->id }}" action="{{ route('projects.destroy', $project) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="confirmDelete(event, 'delete-project-{{ $project->id }}')"
                                        class="w-7 h-7 flex items-center justify-center rounded-full text-secondary hover:text-error hover:bg-error/5 transition-all">
                                    <span class="material-symbols-outlined" style="font-size:16px">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($project->description)
                        <p class="text-secondary text-sm line-clamp-2">{{ $project->description }}</p>
                    @endif

                    {{-- Stats --}}
                    <div class="flex items-center gap-md text-sm text-secondary">
                        <span class="flex items-center gap-xs">
                            <span class="material-symbols-outlined" style="font-size:16px">task_alt</span>
                            {{ $project->tasks_count }} tasks
                        </span>
                        <span class="flex items-center gap-xs">
                            <span class="material-symbols-outlined" style="font-size:16px">sprint</span>
                            {{ $project->sprints_count }} sprints
                        </span>
                    </div>

                    {{-- Active sprint badge --}}
                    @if($activeSprint)
                        <div class="flex items-center gap-sm bg-green-50 border border-green-200 rounded-xl px-sm py-xs">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs text-green-700 font-medium">{{ $activeSprint->name }} — Active Sprint</span>
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-sm border-t border-outline-variant/50">
                        <span class="text-xs text-secondary">Updated {{ $project->updated_at->diffForHumans() }}</span>
                        <a href="{{ route('projects.show', $project) }}"
                           class="flex items-center gap-xs text-xs text-primary font-semibold hover:underline" onclick="event.stopPropagation()">
                            Open <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @endforeach

            {{-- Add new card --}}
            <a href="{{ route('projects.create') }}"
               class="bg-white rounded-2xl border-2 border-dashed border-outline-variant p-lg flex flex-col items-center justify-center gap-sm text-secondary hover:border-primary hover:text-primary hover:bg-primary/5 transition-all duration-200 min-h-[200px]">
                <span class="material-symbols-outlined" style="font-size:32px">add_circle</span>
                <span class="font-semibold text-sm">New Project</span>
            </a>
        </div>
    @endif
</x-layouts.app>
