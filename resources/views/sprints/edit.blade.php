<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-sm text-sm text-secondary mb-xl">
            <a href="{{ route('projects.show', $project) }}" class="hover:text-primary">{{ $project->name }}</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}" class="hover:text-primary">{{ $sprint->name }}</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <span class="text-on-surface font-medium">Edit</span>
        </div>

        <div class="bg-white rounded-2xl border border-outline-variant p-xl shadow-sm">
            <h1 class="text-2xl font-bold text-on-surface mb-xl">Edit Sprint</h1>

            <form action="{{ route('projects.sprints.update', [$project, $sprint]) }}" method="POST" class="flex flex-col gap-lg">
                @csrf @method('PUT')

                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="name">Sprint Name</label>
                    <input id="name" name="name" type="text" required value="{{ old('name', $sprint->name) }}"
                           class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"/>
                </div>

                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="goal">Sprint Goal</label>
                    <textarea id="goal" name="goal" rows="2"
                              class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low resize-none">{{ old('goal', $sprint->goal) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-md">
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="start_date">Start Date</label>
                        <input id="start_date" name="start_date" type="date"
                               value="{{ old('start_date', $sprint->start_date?->format('Y-m-d')) }}"
                               class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"/>
                    </div>
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="end_date">End Date</label>
                        <input id="end_date" name="end_date" type="date"
                               value="{{ old('end_date', $sprint->end_date?->format('Y-m-d')) }}"
                               class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"/>
                    </div>
                </div>

                <div class="flex gap-sm pt-sm border-t border-outline-variant/50">
                    <button type="submit"
                            class="flex items-center gap-sm px-lg py-sm bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-dark transition-all">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save Changes
                    </button>
                    <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}"
                       class="px-lg py-sm border border-outline-variant rounded-xl font-semibold text-sm text-secondary hover:bg-gray-50 transition-all">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
