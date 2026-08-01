<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-sm text-sm text-secondary mb-xl">
            <a href="{{ route('projects.index') }}" class="hover:text-primary">Projects</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <a href="{{ route('projects.show', $project) }}" class="hover:text-primary">{{ $project->name }}</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <a href="{{ route('projects.sprints.index', $project) }}" class="hover:text-primary">Sprints</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <span class="text-on-surface font-medium">New Sprint</span>
        </div>

        <div class="bg-white rounded-2xl border border-outline-variant p-xl shadow-sm">
            <h1 class="text-2xl font-bold text-on-surface mb-xl">Create Sprint</h1>

            <form action="{{ route('projects.sprints.store', $project) }}" method="POST" class="flex flex-col gap-lg">
                @csrf

                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="name">Sprint Name <span class="text-error">*</span></label>
                    <input id="name" name="name" type="text" required value="{{ old('name', 'Sprint ' . ($project->sprints()->count() + 1)) }}"
                           placeholder="e.g. Sprint 1"
                           class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"/>
                    @error('name')<p class="text-error text-xs">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="goal">Sprint Goal</label>
                    <textarea id="goal" name="goal" rows="2"
                              placeholder="What will the team achieve in this sprint?"
                              class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low resize-none">{{ old('goal') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-md">
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="start_date">Start Date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->format('Y-m-d')) }}"
                               class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"/>
                    </div>
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="end_date">End Date</label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date', now()->addWeeks(2)->format('Y-m-d')) }}"
                               class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"/>
                        @error('end_date')<p class="text-error text-xs">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="bg-surface-container-low border border-outline-variant/50 rounded-xl p-md flex items-start gap-sm">
                    <span class="material-symbols-outlined text-primary flex-shrink-0" style="font-size:18px">info</span>
                    <p class="text-sm text-secondary">After creating the sprint, go to the <strong class="text-on-surface">Product Backlog</strong> to move user stories into this sprint.</p>
                </div>

                <div class="flex gap-sm pt-sm border-t border-outline-variant/50">
                    <button type="submit"
                            class="flex items-center gap-sm px-lg py-sm bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-dark active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-base">add_circle</span>
                        Create Sprint
                    </button>
                    <a href="{{ route('projects.show', $project) }}"
                       class="px-lg py-sm border border-outline-variant rounded-xl font-semibold text-sm text-secondary hover:bg-gray-50 transition-all">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
