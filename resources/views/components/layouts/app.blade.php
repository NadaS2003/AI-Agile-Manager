<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'TaskFlow — Agile Project Manager' }}</title>
    <meta name="description" content="Manage your agile projects, sprints, and tasks with TaskFlow."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#3525cd",
                        "primary-dark": "#2418a8",
                        "background": "#f8f9ff",
                        "on-surface": "#0b1c30",
                        "secondary": "#565e74",
                        "surface": "#f8f9ff",
                        "outline-variant": "#c7c4d8",
                        "secondary-container": "#dae2fd",
                        "on-secondary-container": "#5c647a",
                        "surface-container-low": "#eff4ff",
                        "surface-container-lowest": "#ffffff",
                        "primary-container": "#e8e4ff",
                        "on-primary-container": "#1a0e8c",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a",
                        "tertiary": "#005338",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#6ffbbe",
                        "on-tertiary-fixed": "#002113",
                        "tertiary-fixed-dim": "#4edea3",
                        "surface-variant": "#d3e4fe",
                        "on-surface-variant": "#464555",
                        "surface-container-highest": "#d3e4fe",
                        "outline": "#77757f",
                    },
                    spacing: {
                        "base": "4px", "lg": "24px", "sm": "8px", "gutter": "20px",
                        "margin-mobile": "16px", "xs": "4px", "margin-desktop": "40px",
                        "xl": "32px", "md": "16px"
                    },
                    fontFamily: { "body-lg": ["Inter"] }
                },
            },
        }
    </script>
    <style>
        :root { --sidebar-width: 272px; }

        /* Task & project cards */
        .task-card { transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(15,23,42,0.06); }
        .task-card:hover { box-shadow: 0 6px 18px rgba(15,23,42,0.10); transform: translateY(-2px); }
        .completed-task { opacity: 0.6; }

        /* Kanban drag */
        .kanban-col { min-height: 200px; transition: background 0.2s; }
        .kanban-col.drag-over { background: rgba(53,37,205,0.06); border-radius: 12px; }
        .dragging { opacity: 0.4; transform: rotate(2deg); cursor: grabbing; }

        /* Sidebar nav active */
        .nav-active { background: #e8e4ff; color: #3525cd; font-weight: 600; }
        .nav-active .nav-icon { color: #3525cd; }

        /* Smooth scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #c7c4d8; border-radius: 4px; }

        /* Progress bar */
        @keyframes progress-fill { from { width: 0; } }
        .progress-bar { animation: progress-fill 0.8s ease-out; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 2px; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; letter-spacing: 0.02em; }
    </style>
</head>
<body class="bg-background text-on-surface font-body-lg overflow-hidden flex h-screen">

{{-- ══ SIDEBAR ══════════════════════════════════════════════════════════════ --}}
<aside class="fixed h-screen left-0 w-[272px] bg-white border-r border-outline-variant flex flex-col z-40">

    {{-- Brand --}}
    <div class="px-lg pt-lg pb-md border-b border-outline-variant flex-shrink-0">
        <a href="{{ route('projects.index') }}" class="flex items-center gap-sm">
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-base">rocket_launch</span>
            </div>
            <h1 class="font-bold text-xl text-on-surface tracking-tight">TaskFlow</h1>
        </a>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-sm py-md flex flex-col gap-xs">

        {{-- Add Task CTA --}}
        <a href="{{ route('tasks.create') }}"
           class="flex items-center gap-sm px-md py-sm rounded-xl bg-primary text-white font-semibold text-sm mb-sm hover:bg-primary-dark active:scale-95 transition-all shadow-sm">
            <span class="material-symbols-outlined text-base">add</span>
            New Task
        </a>

        {{-- Main links --}}
        <p class="text-xs font-semibold text-secondary uppercase tracking-widest px-md mt-sm mb-xs">Workspace</p>

        <a href="{{ route('projects.index') }}"
           class="flex items-center gap-md px-md py-sm rounded-lg text-sm transition-all {{ request()->routeIs('projects.*') && !request()->routeIs('projects.backlog') && !request()->routeIs('projects.sprints.*') ? 'nav-active' : 'text-secondary hover:bg-gray-100' }}">
            <span class="material-symbols-outlined text-base nav-icon" style="font-size:18px">folder_open</span>
            Projects
        </a>

        <a href="{{ route('tasks.index') }}"
           class="flex items-center gap-md px-md py-sm rounded-lg text-sm transition-all {{ request()->routeIs('tasks.*') ? 'nav-active' : 'text-secondary hover:bg-gray-100' }}">
            <span class="material-symbols-outlined text-base nav-icon" style="font-size:18px">check_circle</span>
            My Tasks
        </a>

        {{-- Projects list --}}
        @php
            $sidebarProjects = \App\Models\Project::where('user_id', auth()->id())
                ->where('status', '!=', 'archived')
                ->latest()->limit(8)->get();
        @endphp

        @if($sidebarProjects->count())
            <p class="text-xs font-semibold text-secondary uppercase tracking-widest px-md mt-lg mb-xs">Recent Projects</p>

            @foreach($sidebarProjects as $sidebarProject)
                <a href="{{ route('projects.show', $sidebarProject) }}"
                   class="flex items-center gap-sm px-md py-sm rounded-lg text-sm transition-all group {{ request()->route('project')?->id === $sidebarProject->id ? 'nav-active' : 'text-secondary hover:bg-gray-100' }}">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $sidebarProject->color }}"></span>
                    <span class="truncate">{{ $sidebarProject->name }}</span>
                </a>
            @endforeach

            <a href="{{ route('projects.index') }}"
               class="flex items-center gap-sm px-md py-xs rounded-lg text-xs text-secondary hover:text-primary transition-colors mt-xs">
                <span class="material-symbols-outlined" style="font-size:14px">more_horiz</span>
                All projects
            </a>
        @endif
    </nav>

    {{-- User avatar --}}
    <div class="px-lg py-md border-t border-outline-variant flex-shrink-0 flex items-center gap-sm">
        <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-primary font-bold">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-secondary truncate">{{ auth()->user()->email }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full text-secondary hover:text-error hover:bg-error/5 transition-all" title="Logout">
                <span class="material-symbols-outlined" style="font-size:18px">logout</span>
            </button>
        </form>
    </div>
</aside>

{{-- ══ MAIN ═════════════════════════════════════════════════════════════════ --}}
<main class="flex-1 ml-[272px] overflow-y-auto bg-background min-h-screen">

    {{-- Top Bar --}}
    <header class="flex justify-between items-center w-full px-margin-desktop py-md sticky top-0 z-30 bg-background/95 backdrop-blur border-b border-outline-variant/50">
        <div class="flex-1 max-w-xl">
            <div class="relative flex items-center bg-surface-container-low rounded-xl px-md py-sm group focus-within:ring-2 focus-within:ring-primary transition-all">
                <span class="material-symbols-outlined text-secondary mr-sm" style="font-size:18px">search</span>
                <input class="bg-transparent border-none outline-none w-full text-sm font-body-lg placeholder:text-outline"
                       placeholder="Search tasks, projects..." type="text" id="global-search"/>
            </div>
        </div>

        <div class="flex items-center gap-md ml-lg">
            {{-- Notifications placeholder --}}
            <button class="w-9 h-9 flex items-center justify-center rounded-full text-secondary hover:bg-surface-variant transition-all">
                <span class="material-symbols-outlined" style="font-size:20px">notifications</span>
            </button>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div id="flash-success"
             class="mx-margin-desktop mt-md px-md py-sm bg-tertiary-fixed text-on-tertiary-fixed rounded-xl flex items-center gap-sm text-sm font-medium shadow-sm">
            <span class="material-symbols-outlined" style="font-size:18px">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mx-margin-desktop mt-md px-md py-sm bg-error-container text-on-error-container rounded-xl flex items-center gap-sm text-sm font-medium">
            <span class="material-symbols-outlined" style="font-size:18px">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Page content --}}
    <div class="px-margin-desktop py-xl max-w-7xl mx-auto">
        {{ $slot }}
    </div>
</main>

<script>
    // Auto-dismiss flash
    setTimeout(() => {
        const flash = document.getElementById('flash-success');
        if (flash) { flash.style.opacity = '0'; flash.style.transition = 'opacity 0.5s'; setTimeout(() => flash.remove(), 500); }
    }, 4000);

    // Confirm delete
    function confirmCardDelete(event, id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3525cd',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            background: '#FFFFFF',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl font-medium px-6 py-2',
                cancelButton: 'rounded-xl font-medium px-6 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    function confirmDelete(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3525cd',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, delete it!',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
</html>
