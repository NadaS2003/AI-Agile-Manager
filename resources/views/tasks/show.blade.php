<x-layouts.app>
    <div class="max-w-[1000px] mx-auto pt-xs px-margin-mobile">

        <div class="flex items-center mb-xl border-b border-outline-variant/30 pb-sm">
            <a href="{{ route('tasks.index') }}" class="flex items-center gap-xs text-secondary hover:text-primary transition-colors text-label-md font-label-md group">
                <span class="material-symbols-outlined text-md transition-transform group-hover:-translate-x-1">arrow_back</span>
                Back to Dashboard
            </a>
        </div>

        <div class="space-y-xl pl-xs">

            <div class="space-y-md">
                <div class="flex items-center gap-sm">
                    <span class="px-sm py-xs text-xs font-bold rounded uppercase tracking-wider
                        {{ $task->status === 'completed' ? 'bg-tertiary-fixed text-on-tertiary-fixed' : '' }}
                        {{ $task->status === 'in_progress' ? 'bg-primary-container text-on-primary-container' : '' }}
                        {{ $task->status === 'pending' ? 'bg-surface-variant text-on-surface-variant' : '' }}">
                        {{ str_replace('_', ' ', $task->status) }}
                    </span>
                </div>

                <h1 class="text-4xl font-bold text-on-surface tracking-tight leading-tight">
                    {{ htmlspecialchars_decode($task->title, ENT_QUOTES) }}
                </h1>
            </div>

            <div class="border-l-4 {{ $task->status === 'completed' ? 'border-tertiary' : ($task->status === 'in_progress' ? 'border-primary' : 'border-outline-variant') }} pl-lg py-sm my-md">
                <span class="block text-xs font-bold text-primary uppercase tracking-wider mb-sm opacity-70">Task Description</span>
                <p class="font-body-md text-on-surface-variant leading-relaxed whitespace-pre-line text-[16px]">
                    {{ $task->description ? htmlspecialchars_decode($task->description, ENT_QUOTES) : 'No description details provided for this task.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-xl py-lg border-t border-b border-outline-variant/30 text-secondary text-xs font-medium">
                <div class="flex items-center gap-xs">
                    <span class="material-symbols-outlined text-sm" style="font-size: 16px;">calendar_today</span>
                    <span>Created: <strong class="text-on-surface-variant font-semibold">{{ $task->created_at->format('M d, Y') }}</strong></span>
                </div>

                <div class="flex items-center gap-xs">
                    <span class="material-symbols-outlined text-sm" style="font-size: 16px;">schedule</span>
                    <span>Updated: <strong class="text-on-surface-variant font-semibold">{{ $task->updated_at->diffForHumans() }}</strong></span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-md">
                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete(event, this)" class="flex items-center gap-xs text-error/80 hover:text-error transition-colors text-sm font-medium">
                        <span class="material-symbols-outlined text-sm" style="font-size: 18px;">delete</span>
                        Delete Task
                    </button>
                </form>

                <a href="{{ route('tasks.edit', $task->id) }}" class="flex items-center gap-xs px-xl py-sm bg-primary text-on-primary text-white rounded-xl font-button text-button shadow-sm hover:bg-primary-container active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-sm" style="font-size: 18px;">edit</span>
                    Edit Details
                </a>
            </div>

        </div>
    </div>
</x-layouts.app>
