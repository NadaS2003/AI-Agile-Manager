<x-layouts.app>
    <div class="max-w-2xl mx-auto">

        <div class="flex items-center gap-sm text-sm text-secondary mb-xl">
            <a href="{{ url()->previous() }}" class="hover:text-primary flex items-center gap-xs">
                <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>
                Back
            </a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <span class="text-on-surface font-medium">Edit Task</span>
        </div>

        <div class="bg-white rounded-2xl border border-outline-variant p-xl shadow-sm">
            <h1 class="text-2xl font-bold text-on-surface mb-xl">Edit Task</h1>

            <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="flex flex-col gap-lg">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="title">Title <span class="text-error">*</span></label>
                    <input id="title" name="title" type="text" required
                           value="{{ old('title', htmlspecialchars_decode($task->title, ENT_QUOTES)) }}"
                           class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"/>
                    @error('title')<p class="text-error text-xs">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="description">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low resize-none">{{ old('description', htmlspecialchars_decode($task->description, ENT_QUOTES)) }}</textarea>
                </div>

                {{-- Status --}}
                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="status">Status</label>
                    <select id="status" name="status"
                            class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low">
                        <option value="pending"     {{ old('status', $task->status) === 'pending'     ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>🔄 In Progress</option>
                        <option value="completed"   {{ old('status', $task->status) === 'completed'   ? 'selected' : '' }}>✅ Completed</option>
                    </select>
                </div>

                {{-- Project & Sprint --}}
                <div class="grid grid-cols-2 gap-md">
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="project_id">Project</label>
                        <select id="project_id" name="project_id"
                                class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"
                                onchange="loadSprints(this.value)">
                            <option value="">— No Project —</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="sprint_id">Sprint</label>
                        <select id="sprint_id" name="sprint_id"
                                class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low">
                            <option value="">— Backlog —</option>
                            @foreach($sprints as $sprint)
                                <option value="{{ $sprint->id }}" {{ old('sprint_id', $task->sprint_id) == $sprint->id ? 'selected' : '' }}>
                                    {{ $sprint->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Priority & Story Points --}}
                <div class="grid grid-cols-2 gap-md">
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="priority">Priority</label>
                        <select id="priority" name="priority"
                                class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low">
                            <option value="low"      {{ old('priority', $task->priority) === 'low'      ? 'selected' : '' }}>🔽 Low</option>
                            <option value="medium"   {{ old('priority', $task->priority) === 'medium'   ? 'selected' : '' }}>➖ Medium</option>
                            <option value="high"     {{ old('priority', $task->priority) === 'high'     ? 'selected' : '' }}>🔼 High</option>
                            <option value="critical" {{ old('priority', $task->priority) === 'critical' ? 'selected' : '' }}>🔥 Critical</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="story_points">Story Points</label>
                        <input id="story_points" name="story_points" type="number" min="1" max="100"
                               value="{{ old('story_points', $task->story_points) }}"
                               placeholder="e.g. 3"
                               class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low placeholder:text-outline"/>
                    </div>
                </div>

                <div class="flex gap-sm pt-sm border-t border-outline-variant/50">
                    <button type="submit"
                            class="flex items-center gap-sm px-lg py-sm bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-dark active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save Changes
                    </button>
                    <a href="{{ url()->previous() }}"
                       class="px-lg py-sm border border-outline-variant rounded-xl font-semibold text-sm text-secondary hover:bg-gray-50 transition-all">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        async function loadSprints(projectId) {
            const sprintSelect = document.getElementById('sprint_id');
            const currentSprint = '{{ $task->sprint_id }}';
            sprintSelect.innerHTML = '<option value="">— Backlog —</option>';
            if (!projectId) return;
            try {
                const res = await fetch(`/projects/${projectId}/sprints-json`);
                const sprints = await res.json();
                sprints.forEach(sprint => {
                    const opt = document.createElement('option');
                    opt.value = sprint.id;
                    opt.textContent = sprint.name + (sprint.status === 'active' ? ' ⚡' : '');
                    if (sprint.id == currentSprint) opt.selected = true;
                    sprintSelect.appendChild(opt);
                });
            } catch (e) { console.error(e); }
        }

        // Load sprints on page load if project is set
        const initProject = document.getElementById('project_id').value;
        if (initProject) loadSprints(initProject);
    </script>
</x-layouts.app>
