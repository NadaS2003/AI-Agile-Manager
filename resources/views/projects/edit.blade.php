<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-sm text-sm text-secondary mb-xl">
            <a href="{{ route('projects.index') }}" class="hover:text-primary">Projects</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <a href="{{ route('projects.show', $project) }}" class="hover:text-primary">{{ $project->name }}</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <span class="text-on-surface font-medium">Edit</span>
        </div>

        <div class="bg-white rounded-2xl border border-outline-variant p-xl shadow-sm">
            <h1 class="text-2xl font-bold text-on-surface mb-xl">Edit Project</h1>

            <form action="{{ route('projects.update', $project) }}" method="POST" class="flex flex-col gap-lg">
                @csrf @method('PUT')

                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="name">Project Name <span class="text-error">*</span></label>
                    <input id="name" name="name" type="text" required value="{{ old('name', $project->name) }}"
                           class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low"/>
                    @error('name')<p class="text-error text-xs">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-col gap-xs">
                    <label class="text-sm font-semibold text-on-surface" for="description">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low resize-none">{{ old('description', $project->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-md">
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="color">Project Color</label>
                        <div class="flex items-center gap-sm">
                            <input id="color" name="color" type="color" value="{{ old('color', $project->color) }}"
                                   class="w-10 h-10 rounded-lg border border-outline-variant cursor-pointer"/>
                            <div class="flex gap-xs flex-wrap">
                                @foreach(['#3525cd','#e11d48','#16a34a','#d97706','#7c3aed','#0891b2','#be185d','#374151'] as $c)
                                    <button type="button" onclick="document.getElementById('color').value='{{ $c }}'"
                                            class="w-6 h-6 rounded-full border-2 border-white shadow-sm hover:scale-110 transition-transform"
                                            style="background: {{ $c }}"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="status">Status</label>
                        <select id="status" name="status"
                                class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-low">
                            @foreach(['planning' => 'Planning', 'active' => 'Active', 'on_hold' => 'On Hold', 'completed' => 'Completed', 'archived' => 'Archived'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $project->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-sm pt-sm border-t border-outline-variant/50">
                    <button type="submit"
                            class="flex items-center gap-sm px-lg py-sm bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-dark transition-all">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save Changes
                    </button>
                    <a href="{{ route('projects.show', $project) }}"
                       class="px-lg py-sm border border-outline-variant rounded-xl font-semibold text-sm text-secondary hover:bg-gray-50 transition-all">
                        Cancel
                    </a>

                    <button type="button"
                            onclick="confirmDelete(event, 'delete-project-form-{{ $project->id }}')"
                            class="ml-auto flex items-center gap-xs px-md py-sm border border-error/30 text-error rounded-xl text-sm font-semibold hover:bg-error/5 transition-all">
                        <span class="material-symbols-outlined" style="font-size:16px">delete</span>
                        Delete Project
                    </button>
                </div>
            </form>

            <form id="delete-project-form-{{ $project->id }}" action="{{ route('projects.destroy', $project) }}" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>
        </div>
    </div>
</x-layouts.app>
