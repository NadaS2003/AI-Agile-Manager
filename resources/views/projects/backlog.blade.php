<x-layouts.app>
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-sm text-sm text-secondary mb-lg">
        <a href="{{ route('projects.index') }}" class="hover:text-primary">Projects</a>
        <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        <a href="{{ route('projects.show', $project) }}" class="hover:text-primary">{{ $project->name }}</a>
        <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        <span class="text-on-surface font-medium">Backlog</span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-xl">
        <div>
            <h1 class="text-2xl font-bold text-on-surface flex items-center gap-sm">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background:{{ $project->color }}">
                    {{ strtoupper(substr($project->name, 0, 1)) }}
                </div>
                Product Backlog
            </h1>
            <p class="text-secondary text-sm mt-xs">{{ $backlogTasks->count() }} items not yet assigned to a sprint</p>
        </div>

        <div class="flex items-center gap-sm">
            <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}"
               class="flex items-center gap-xs px-md py-sm bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-all">
                <span class="material-symbols-outlined" style="font-size:16px">add</span>
                Add Story
            </a>
        </div>
    </div>

    @if($backlogTasks->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <span class="material-symbols-outlined text-secondary mb-md" style="font-size:48px">list_alt</span>
            <h2 class="font-bold text-on-surface mb-sm">Backlog is empty</h2>
            <p class="text-secondary text-sm mb-lg">Add user stories to the backlog, then assign them to sprints.</p>
            <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}"
               class="flex items-center gap-xs px-lg py-sm bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-all">
                <span class="material-symbols-outlined" style="font-size:16px">add</span>
                Add First Story
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-outline-variant overflow-hidden">
            {{-- Table header --}}
            <div class="grid grid-cols-12 gap-sm px-lg py-sm bg-surface-container-low border-b border-outline-variant text-xs font-semibold text-secondary uppercase tracking-wider">
                <div class="col-span-5">Story</div>
                <div class="col-span-2">Priority</div>
                <div class="col-span-2">Points</div>
                <div class="col-span-2">Move to Sprint</div>
                <div class="col-span-1"></div>
            </div>

            {{-- Backlog items --}}
            @foreach($backlogTasks as $task)
                <div class="grid grid-cols-12 gap-sm items-center px-lg py-md border-b border-outline-variant/50 hover:bg-surface-container-low/50 transition-colors group">

                    {{-- Story title & desc --}}
                    <div class="col-span-5">
                        <p class="font-semibold text-sm text-on-surface">{{ $task->title }}</p>
                        @if($task->description)
                            <p class="text-xs text-secondary line-clamp-1 mt-xs">{{ $task->description }}</p>
                        @endif
                    </div>

                    {{-- Priority --}}
                    <div class="col-span-2">
                        <span class="badge {{ $task->priorityColor }}">
                            <span class="material-symbols-outlined" style="font-size:11px">{{ $task->priorityIcon }}</span>
                            {{ $task->priorityLabel }}
                        </span>
                    </div>

                    {{-- Story points --}}
                    <div class="col-span-2">
                        @if($task->story_points)
                            <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">
                                {{ $task->story_points }}
                            </span>
                        @else
                            <span class="text-xs text-secondary italic">—</span>
                        @endif
                    </div>

                    {{-- Move to sprint --}}
                    <div class="col-span-2">
                        @if($sprints->count())
                            <form action="{{ route('tasks.moveToSprint', $task) }}" method="POST" class="flex gap-xs">
                                @csrf
                                <select name="sprint_id"
                                        class="text-xs border border-outline-variant rounded-lg px-xs py-1 bg-surface-container-low focus:outline-none focus:ring-1 focus:ring-primary flex-1">
                                    @foreach($sprints as $sprint)
                                        <option value="{{ $sprint->id }}">{{ $sprint->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                        class="px-xs py-1 bg-primary text-white rounded-lg text-xs hover:bg-primary-dark transition-all"
                                        title="Move to Sprint">
                                    <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-secondary italic">No sprints yet</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="col-span-1 flex items-center gap-xs justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('tasks.edit', $task) }}"
                           class="w-7 h-7 flex items-center justify-center text-secondary hover:text-amber-600 hover:bg-amber-50 rounded-full transition-all">
                            <span class="material-symbols-outlined" style="font-size:16px">edit</span>
                        </a>
                        <form id="delete-form-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmCardDelete(event, '{{ $task->id }}')"
                                    class="w-7 h-7 flex items-center justify-center text-secondary hover:text-error hover:bg-error/5 rounded-full transition-all">
                                <span class="material-symbols-outlined" style="font-size:16px">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Totals footer --}}
        <div class="flex items-center gap-lg mt-md text-sm text-secondary">
            <span>{{ $backlogTasks->count() }} items</span>
            <span>·</span>
            <span>{{ $backlogTasks->sum('story_points') }} story points total</span>
        </div>
    @endif
</x-layouts.app>
