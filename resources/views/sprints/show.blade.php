<x-layouts.app>
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-sm text-sm text-secondary mb-lg">
        <a href="{{ route('projects.index') }}" class="hover:text-primary">Projects</a>
        <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        <a href="{{ route('projects.show', $project) }}" class="hover:text-primary">{{ $project->name }}</a>
        <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        <a href="{{ route('projects.sprints.index', $project) }}" class="hover:text-primary">Sprints</a>
        <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        <span class="text-on-surface font-medium">{{ $sprint->name }}</span>
    </div>

    {{-- Sprint Header --}}
    <div class="flex items-start justify-between mb-lg">
        <div>
            <div class="flex items-center gap-sm">
                <h1 class="text-2xl font-bold text-on-surface">{{ $sprint->name }}</h1>
                <span class="badge {{ $sprint->statusColor }}">{{ $sprint->statusLabel }}</span>
            </div>
            @if($sprint->goal)
                <p class="text-secondary text-sm mt-xs max-w-xl">🎯 {{ $sprint->goal }}</p>
            @endif
            @if($sprint->start_date)
                <p class="text-xs text-secondary mt-xs">
                    {{ $sprint->start_date->format('M d') }} — {{ $sprint->end_date?->format('M d, Y') ?? 'No end date' }}
                    @if($sprint->status === 'active' && $sprint->end_date)
                        · <span class="{{ $sprint->daysLeft <= 2 ? 'text-error font-semibold' : '' }}">{{ $sprint->daysLeft }} days left</span>
                    @endif
                </p>
            @endif
        </div>

        <div class="flex items-center gap-sm">
            @if($sprint->status === 'planning')
                <form action="{{ route('projects.sprints.start', [$project, $sprint]) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-xs px-md py-sm bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 active:scale-95 transition-all">
                        <span class="material-symbols-outlined" style="font-size:16px">play_arrow</span>
                        Start Sprint
                    </button>
                </form>
            @elseif($sprint->status === 'active')
                <form action="{{ route('projects.sprints.complete', [$project, $sprint]) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-xs px-md py-sm bg-purple-600 text-white rounded-xl text-sm font-semibold hover:bg-purple-700 active:scale-95 transition-all">
                        <span class="material-symbols-outlined" style="font-size:16px">flag</span>
                        Complete Sprint
                    </button>
                </form>
            @endif

            <a href="{{ route('tasks.create', ['project_id' => $project->id, 'sprint_id' => $sprint->id]) }}"
               class="flex items-center gap-xs px-md py-sm bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-all">
                <span class="material-symbols-outlined" style="font-size:16px">add</span>
                Add Task
            </a>

            <a href="{{ route('projects.sprints.edit', [$project, $sprint]) }}"
               class="w-9 h-9 flex items-center justify-center border border-outline-variant rounded-xl text-secondary hover:text-amber-600 hover:bg-amber-50 transition-all">
                <span class="material-symbols-outlined" style="font-size:18px">edit</span>
            </a>
        </div>
    </div>

    {{-- Sprint Progress Bar --}}
    <div class="bg-white rounded-xl border border-outline-variant p-md mb-xl">
        <div class="flex items-center justify-between text-sm mb-sm">
            <div class="flex items-center gap-lg">
                <span class="text-secondary">Sprint Progress</span>
                <div class="flex items-center gap-md text-xs">
                    <span class="flex items-center gap-xs text-secondary"><span class="w-2 h-2 rounded-full bg-surface-variant inline-block"></span> To Do: {{ $todoTasks->count() }}</span>
                    <span class="flex items-center gap-xs text-primary"><span class="w-2 h-2 rounded-full bg-primary inline-block"></span> In Progress: {{ $inProgressTasks->count() }}</span>
                    <span class="flex items-center gap-xs text-tertiary"><span class="w-2 h-2 rounded-full bg-tertiary-fixed-dim inline-block"></span> Done: {{ $doneTasks->count() }}</span>
                </div>
            </div>
            <span class="font-bold text-on-surface">{{ $completedPoints }}/{{ $totalPoints }} pts — {{ $progress }}%</span>
        </div>
        <div class="h-2.5 bg-surface-variant rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-primary to-indigo-400 rounded-full progress-bar transition-all duration-700"
                 style="width: {{ $progress }}%"></div>
        </div>
    </div>

    {{-- ══ KANBAN BOARD ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-3 gap-md" id="kanban-board">

        @php
            $columns = [
                ['id' => 'pending',     'label' => 'To Do',      'icon' => 'radio_button_unchecked', 'color' => 'text-secondary',  'bg' => 'bg-surface-container-low',      'tasks' => $todoTasks],
                ['id' => 'in_progress', 'label' => 'In Progress', 'icon' => 'pending',               'color' => 'text-primary',    'bg' => 'bg-primary-container/50',       'tasks' => $inProgressTasks],
                ['id' => 'completed',   'label' => 'Done',        'icon' => 'check_circle',          'color' => 'text-tertiary',   'bg' => 'bg-tertiary-fixed/20',          'tasks' => $doneTasks],
            ];
        @endphp

        @foreach($columns as $col)
            <div class="flex flex-col gap-sm">

                {{-- Column header --}}
                <div class="flex items-center justify-between px-sm">
                    <div class="flex items-center gap-sm">
                        <span class="material-symbols-outlined {{ $col['color'] }}" style="font-size:18px">{{ $col['icon'] }}</span>
                        <h2 class="font-bold text-sm text-on-surface">{{ $col['label'] }}</h2>
                        <span class="w-5 h-5 rounded-full bg-surface-variant text-secondary text-xs font-bold flex items-center justify-center">{{ $col['tasks']->count() }}</span>
                    </div>
                </div>

                {{-- Drop zone --}}
                <div class="kanban-col flex flex-col gap-sm p-sm rounded-2xl {{ $col['bg'] }} border border-outline-variant/50 min-h-[300px]"
                     data-status="{{ $col['id'] }}"
                     ondragover="onDragOver(event)"
                     ondragleave="onDragLeave(event)"
                     ondrop="onDrop(event, '{{ $col['id'] }}')">

                    @foreach($col['tasks'] as $task)
                        <div class="kanban-card task-card bg-white rounded-xl border border-outline-variant p-md cursor-grab select-none"
                             draggable="true"
                             data-task-id="{{ $task->id }}"
                             ondragstart="onDragStart(event, {{ $task->id }})"
                             ondragend="onDragEnd(event)"
                             id="kanban-task-{{ $task->id }}">

                            {{-- Priority & points --}}
                            <div class="flex items-center justify-between mb-sm">
                                <span class="badge {{ $task->priorityColor }} text-xs">
                                    <span class="material-symbols-outlined" style="font-size:11px">{{ $task->priorityIcon }}</span>
                                    {{ $task->priorityLabel }}
                                </span>
                                @if($task->story_points)
                                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold"
                                          title="{{ $task->story_points }} story points">
                                        {{ $task->story_points }}
                                    </span>
                                @endif
                            </div>

                            {{-- Title --}}
                            <h3 class="font-semibold text-sm text-on-surface mb-xs {{ $col['id'] === 'completed' ? 'line-through opacity-60' : '' }}">
                                {{ $task->title }}
                            </h3>

                            @if($task->description)
                                <p class="text-xs text-secondary line-clamp-2 mb-sm">{{ $task->description }}</p>
                            @endif

                            {{-- Footer --}}
                            <div class="flex items-center justify-between mt-sm pt-sm border-t border-outline-variant/30">
                                <span class="text-xs text-secondary flex items-center gap-xs">
                                    <span class="material-symbols-outlined" style="font-size:12px">event</span>
                                    {{ $task->created_at->format('M d') }}
                                </span>
                                <div class="flex items-center gap-xs opacity-0 group-hover:opacity-100 transition-opacity kanban-actions">
                                    <a href="{{ route('tasks.edit', $task) }}"
                                       class="w-6 h-6 flex items-center justify-center rounded-full text-secondary hover:text-amber-600 hover:bg-amber-50 transition-all"
                                       title="Edit">
                                        <span class="material-symbols-outlined" style="font-size:14px">edit</span>
                                    </a>
                                    <form id="delete-form-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmCardDelete(event, '{{ $task->id }}')"
                                                class="w-6 h-6 flex items-center justify-center rounded-full text-secondary hover:text-error hover:bg-error/5 transition-all"
                                                title="Delete">
                                            <span class="material-symbols-outlined" style="font-size:14px">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Empty column placeholder --}}
                    @if($col['tasks']->count() === 0)
                        <div class="flex flex-col items-center justify-center py-lg text-secondary text-xs text-center opacity-60 pointer-events-none">
                            <span class="material-symbols-outlined mb-xs" style="font-size:24px">{{ $col['icon'] }}</span>
                            Drop tasks here
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- CSRF Token for AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        let draggedTaskId = null;

        // Show actions on hover
        document.querySelectorAll('.kanban-card').forEach(card => {
            card.addEventListener('mouseenter', () => card.querySelector('.kanban-actions')?.classList.remove('opacity-0'));
            card.addEventListener('mouseleave', () => card.querySelector('.kanban-actions')?.classList.add('opacity-0'));
        });

        function onDragStart(event, taskId) {
            draggedTaskId = taskId;
            event.dataTransfer.effectAllowed = 'move';
            event.target.classList.add('dragging');
            event.dataTransfer.setData('text/plain', taskId);
        }

        function onDragEnd(event) {
            event.target.classList.remove('dragging');
            document.querySelectorAll('.kanban-col').forEach(col => col.classList.remove('drag-over'));
        }

        function onDragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            event.currentTarget.classList.add('drag-over');
        }

        function onDragLeave(event) {
            event.currentTarget.classList.remove('drag-over');
        }

        function onDrop(event, newStatus) {
            event.preventDefault();
            const col = event.currentTarget;
            col.classList.remove('drag-over');

            const taskId = draggedTaskId || event.dataTransfer.getData('text/plain');
            if (!taskId) return;

            const card = document.getElementById('kanban-task-' + taskId);
            if (!card) return;

            // Move card in DOM
            const placeholder = col.querySelector('.pointer-events-none');
            if (placeholder) placeholder.remove();
            col.appendChild(card);

            // Update status via AJAX
            fetch(`/tasks/${taskId}/kanban`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Update visual state
                    const title = card.querySelector('h3');
                    if (newStatus === 'completed') {
                        title?.classList.add('line-through', 'opacity-60');
                    } else {
                        title?.classList.remove('line-through', 'opacity-60');
                    }
                    updateColumnCounts();
                }
            })
            .catch(err => console.error('Kanban update failed:', err));

            draggedTaskId = null;
        }

        function updateColumnCounts() {
            document.querySelectorAll('.kanban-col').forEach(col => {
                const count = col.querySelectorAll('.kanban-card').length;
                const header = col.closest('.flex.flex-col').querySelector('.rounded-full');
                if (header) header.textContent = count;
            });
        }
    </script>
</x-layouts.app>
