<x-layouts.app>
    <div class="max-w-2xl mx-auto">

        <div class="flex items-center gap-sm text-sm text-secondary mb-xl">
            <a href="{{ route('tasks.index') }}" class="hover:text-primary">Tasks</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <span class="text-on-surface font-medium">New Task</span>
        </div>

        <div class="bg-white rounded-2xl border border-outline-variant p-xl shadow-sm">
            <h1 class="text-2xl font-bold text-on-surface mb-xl">Create Task</h1>

            <form action="{{ route('tasks.store') }}" method="POST" class="flex flex-col gap-lg">
                @csrf

                {{-- Title --}}
                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="title">Title <span class="text-error">*</span></label>
                    <input id="title" name="title" type="text" required value="{{ old('title') }}"
                           placeholder="What needs to be done?"
                           class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low placeholder:text-outline"/>
                    @error('title')<p class="text-error text-xs">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="description">Description</label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Add more details..."
                              class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low placeholder:text-outline resize-none">{{ old('description') }}</textarea>
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
                                <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="sprint_id">Sprint (optional)</label>
                        <select id="sprint_id" name="sprint_id"
                                class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low">
                            <option value="">— Backlog —</option>
                            @if(request('sprint_id'))
                                {{-- Pre-select if passed via query string --}}
                                <option value="{{ request('sprint_id') }}" selected>Sprint #{{ request('sprint_id') }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                {{-- Priority & Story Points --}}
                <div class="grid grid-cols-2 gap-md">
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="priority">Priority</label>
                        <select id="priority" name="priority"
                                class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low">
                            <option value="low"      {{ old('priority') === 'low'      ? 'selected' : '' }}>🔽 Low</option>
                            <option value="medium"   {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>➖ Medium</option>
                            <option value="high"     {{ old('priority') === 'high'     ? 'selected' : '' }}>🔼 High</option>
                            <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>🔥 Critical</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="story_points">
                            Story Points
                            <span class="text-secondary font-normal text-xs">(Fibonacci: 1, 2, 3, 5, 8, 13...)</span>
                        </label>
                        <input id="story_points" name="story_points" type="number" min="1" max="100"
                               value="{{ old('story_points') }}" placeholder="e.g. 3"
                               class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low placeholder:text-outline"/>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-sm pt-sm border-t border-outline-variant/50">
                    <button type="submit"
                            class="flex items-center gap-sm px-lg py-sm bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-dark active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-base">add_task</span>
                        Create Task
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
        // Load sprints for selected project
        async function loadSprints(projectId) {
            const sprintSelect = document.getElementById('sprint_id');
            sprintSelect.innerHTML = '<option value="">— Backlog —</option>';

            if (!projectId) return;

            try {
                const res = await fetch(`/projects/${projectId}/sprints-json`);
                const sprints = await res.json();
                sprints.forEach(sprint => {
                    const opt = document.createElement('option');
                    opt.value = sprint.id;
                    opt.textContent = sprint.name + (sprint.status === 'active' ? ' ⚡ Active' : '');
                    sprintSelect.appendChild(opt);
                });

                // Auto-select if sprint_id passed in query
                const preselect = '{{ request('sprint_id') }}';
                if (preselect) sprintSelect.value = preselect;
            } catch (e) {
                console.error(e);
            }
        }

        // On page load, if project is preselected, load its sprints
        const preSelectedProject = document.getElementById('project_id').value;
        if (preSelectedProject) loadSprints(preSelectedProject);
    </script>
</x-layouts.app>
