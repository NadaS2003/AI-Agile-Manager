<x-layouts.app>
    <div class="max-w-2xl mx-auto">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-sm text-sm text-secondary mb-xl">
            <a href="{{ route('projects.index') }}" class="hover:text-primary transition-colors">Projects</a>
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
            <span class="text-on-surface font-medium">New Project</span>
        </div>

        {{-- Mode Selector Cards --}}
        <div class="mb-xl">
            <h1 class="text-2xl font-bold text-on-surface">Create New Project</h1>
            <p class="text-secondary text-sm mt-xs">Choose how you want to set up your Agile project workflow</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-md mb-xl">
            <!-- Option A: Manual Creation -->
            <button type="button" id="btn-mode-manual" onclick="setMode('manual')"
                    class="text-left p-lg rounded-2xl border-2 border-primary bg-primary/5 transition-all duration-200 focus:outline-none flex flex-col justify-between min-h-[160px]">
                <div>
                    <div class="flex items-center gap-md mb-sm">
                        <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined">design_services</span>
                        </div>
                        <h3 class="font-bold text-on-surface">Build Manually</h3>
                    </div>
                    <p class="text-secondary text-xs leading-relaxed">Traditional setup. Input project details, manually build your backlog, assign user stories, story points, and priorities.</p>
                </div>
                <span class="text-xs text-primary font-semibold flex items-center gap-xs mt-md">
                    Select Mode <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </span>
            </button>

            <!-- Option B: AI Generation -->
            <button type="button" id="btn-mode-ai" onclick="setMode('ai')"
                    class="text-left p-lg rounded-2xl border-2 border-outline-variant hover:border-primary/50 transition-all duration-200 focus:outline-none flex flex-col justify-between min-h-[160px]">
                <div>
                    <div class="flex items-center gap-md mb-sm">
                        <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined">auto_awesome</span>
                        </div>
                        <h3 class="font-bold text-on-surface">Generate with AI</h3>
                    </div>
                    <p class="text-secondary text-xs leading-relaxed">Smart setup. Input a project concept, and let our AI Scrum Master automatically generate sprints, goals, user stories, and points.</p>
                </div>
                <span class="text-xs text-secondary font-semibold flex items-center gap-xs mt-md group-hover:text-primary">
                    Select Mode <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </span>
            </button>
        </div>

        {{-- Container for Forms --}}
        <div class="bg-white rounded-2xl border border-outline-variant p-xl shadow-sm relative">

            <!-- Manual Form -->
            <div id="manual-form-container">
                <form action="{{ route('projects.store') }}" method="POST" class="flex flex-col gap-lg">
                    @csrf

                    {{-- Name --}}
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="name">Project Name <span class="text-error">*</span></label>
                        <input id="name" name="name" type="text" required
                               value="{{ old('name') }}"
                               placeholder="e.g. E-commerce Platform v2"
                               class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-surface-container-low placeholder:text-outline"/>
                        @error('name')<p class="text-error text-xs">{{ $message }}</p>@enderror
                    </div>

                    {{-- Description --}}
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="description">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="What is this project about?"
                                  class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-surface-container-low placeholder:text-outline resize-none">{{ old('description') }}</textarea>
                    </div>

                    {{-- Color & Status --}}
                    <div class="grid grid-cols-2 gap-md">
                        <div class="flex flex-col gap-xs">
                            <label class="text-sm font-semibold text-on-surface" for="color">Project Color</label>
                            <div class="flex items-center gap-sm">
                                <input id="color" name="color" type="color" value="{{ old('color', '#3525cd') }}"
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
                                <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            </select>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-sm pt-sm border-t border-outline-variant/50">
                        <button type="submit"
                                class="flex items-center gap-sm px-lg py-sm bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-dark active:scale-95 transition-all">
                            <span class="material-symbols-outlined text-base">rocket_launch</span>
                            Create Project
                        </button>
                        <a href="{{ route('projects.index') }}"
                           class="px-lg py-sm border border-outline-variant rounded-xl font-semibold text-sm text-secondary hover:bg-gray-50 transition-all">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- AI Form -->
            <div id="ai-form-container" class="hidden">
                <form action="{{ route('projects.generate-ai') }}" method="POST" class="flex flex-col gap-lg" onsubmit="showLoadingOverlay()">
                    @csrf

                    {{-- Prompt Idea --}}
                    <div class="flex flex-col gap-xs">
                        <label class="text-sm font-semibold text-on-surface" for="idea">Project Concept or Idea <span class="text-error">*</span></label>
                        <textarea id="idea" name="idea" rows="5" required
                                  placeholder="Describe your project concept in detail (e.g. A mobile app for local food delivery that allows users to order from home kitchens, track drivers, and review meals...)"
                                  class="w-full px-md py-sm border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all bg-surface-container-low placeholder:text-outline resize-none">{{ old('idea') }}</textarea>
                        @error('idea')<p class="text-error text-xs">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-purple-50 border border-purple-200 rounded-2xl p-md flex items-start gap-sm">
                        <span class="material-symbols-outlined text-purple-600 flex-shrink-0" style="font-size:20px">info</span>
                        <div class="text-xs text-purple-900 leading-relaxed">
                            <strong>AI Planning Scope:</strong> Our AI engine will structure your raw description into:
                            <ul class="list-disc ml-md mt-xs flex flex-col gap-xs">
                                <li>A realistic project title and scope summary</li>
                                <li>2 to 4 logical Sprints with goals and sequenced timeframes</li>
                                <li>Fully mapped user stories containing description and acceptance criteria</li>
                                <li>Fibonacci story points and priority ratings (low to critical)</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-sm pt-sm border-t border-outline-variant/50">
                        <button type="submit"
                                class="flex items-center gap-sm px-lg py-sm bg-purple-600 text-white rounded-xl font-semibold text-sm hover:bg-purple-700 active:scale-95 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-base">auto_awesome</span>
                            Generate Project with AI
                        </button>
                        <a href="{{ route('projects.index') }}"
                           class="px-lg py-sm border border-outline-variant rounded-xl font-semibold text-sm text-secondary hover:bg-gray-50 transition-all">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Interactive Loading Overlay --}}
    <div id="loading-overlay" class="hidden fixed inset-0 bg-on-surface/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl p-xl shadow-xl max-w-sm w-full text-center flex flex-col items-center gap-md">
            <!-- Spinner -->
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 rounded-full border-4 border-purple-100"></div>
                <div class="absolute inset-0 rounded-full border-4 border-purple-600 border-t-transparent animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 animate-pulse">auto_awesome</span>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-lg text-on-surface">AI Scrum Master at work</h3>
                <p class="text-secondary text-sm mt-xs" id="loading-msg">Analyzing concept & planning sprints...</p>
            </div>
            <!-- Progress Steps animation -->
            <div class="flex flex-col gap-xs w-full text-left text-xs text-secondary mt-sm pt-sm border-t border-outline-variant/30">
                <div class="flex items-center gap-sm" id="step-1">
                    <span class="material-symbols-outlined text-purple-600 text-sm animate-ping">radio_button_checked</span>
                    <span>Analyzing your concept...</span>
                </div>
                <div class="flex items-center gap-sm opacity-50" id="step-2">
                    <span class="material-symbols-outlined text-sm">radio_button_unchecked</span>
                    <span>Formulating project backlog...</span>
                </div>
                <div class="flex items-center gap-sm opacity-50" id="step-3">
                    <span class="material-symbols-outlined text-sm">radio_button_unchecked</span>
                    <span>Organizing Sprints & stories...</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setMode(mode) {
            const btnManual = document.getElementById('btn-mode-manual');
            const btnAi = document.getElementById('btn-mode-ai');
            const formManual = document.getElementById('manual-form-container');
            const formAi = document.getElementById('ai-form-container');

            if (mode === 'manual') {
                btnManual.classList.add('border-primary', 'bg-primary/5');
                btnManual.classList.remove('border-outline-variant');
                btnManual.querySelector('span.text-xs').classList.add('text-primary');
                btnManual.querySelector('span.text-xs').classList.remove('text-secondary');

                btnAi.classList.remove('border-primary', 'bg-primary/5');
                btnAi.classList.add('border-outline-variant');
                btnAi.querySelector('span.text-xs').classList.remove('text-primary');
                btnAi.querySelector('span.text-xs').classList.add('text-secondary');

                formManual.classList.remove('hidden');
                formAi.classList.add('hidden');
            } else {
                btnAi.classList.add('border-primary', 'bg-primary/5');
                btnAi.classList.remove('border-outline-variant');
                btnAi.querySelector('span.text-xs').classList.add('text-primary');
                btnAi.querySelector('span.text-xs').classList.remove('text-secondary');

                btnManual.classList.remove('border-primary', 'bg-primary/5');
                btnManual.classList.add('border-outline-variant');
                btnManual.querySelector('span.text-xs').classList.remove('text-primary');
                btnManual.querySelector('span.text-xs').classList.add('text-secondary');

                formManual.classList.add('hidden');
                formAi.classList.remove('hidden');
            }
        }

        function showLoadingOverlay() {
            document.getElementById('loading-overlay').classList.remove('hidden');
            let step = 1;
            setInterval(() => {
                step++;
                if (step === 2) {
                    document.getElementById('step-1').classList.remove('opacity-100');
                    document.getElementById('step-1').classList.add('opacity-50');
                    document.getElementById('step-1').querySelector('.material-symbols-outlined').textContent = 'check_circle';
                    document.getElementById('step-1').querySelector('.material-symbols-outlined').classList.remove('animate-ping', 'text-purple-600');
                    document.getElementById('step-1').querySelector('.material-symbols-outlined').classList.add('text-green-600');
                    
                    document.getElementById('step-2').classList.remove('opacity-50');
                    document.getElementById('step-2').classList.add('opacity-100');
                    document.getElementById('step-2').querySelector('.material-symbols-outlined').textContent = 'radio_button_checked';
                    document.getElementById('step-2').querySelector('.material-symbols-outlined').classList.add('animate-ping', 'text-purple-600');
                    document.getElementById('loading-msg').textContent = 'Structuring project backlog...';
                } else if (step === 3) {
                    document.getElementById('step-2').classList.remove('opacity-100');
                    document.getElementById('step-2').classList.add('opacity-50');
                    document.getElementById('step-2').querySelector('.material-symbols-outlined').textContent = 'check_circle';
                    document.getElementById('step-2').querySelector('.material-symbols-outlined').classList.remove('animate-ping', 'text-purple-600');
                    document.getElementById('step-2').querySelector('.material-symbols-outlined').classList.add('text-green-600');

                    document.getElementById('step-3').classList.remove('opacity-50');
                    document.getElementById('step-3').classList.add('opacity-100');
                    document.getElementById('step-3').querySelector('.material-symbols-outlined').textContent = 'radio_button_checked';
                    document.getElementById('step-3').querySelector('.material-symbols-outlined').classList.add('animate-ping', 'text-purple-600');
                    document.getElementById('loading-msg').textContent = 'Generating sprints & stories...';
                }
            }, 3000);
        }

        // Handle error restoration of tab
        @if($errors->has('idea') || session('error'))
            setMode('ai');
        @endif
    </script>
</x-layouts.app>
