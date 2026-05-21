<!-- Sidebar Container -->
<aside id="sidebar" class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-zinc-900 border-r border-slate-200/80 dark:border-zinc-800/80 z-50 transform -translate-x-full lg:translate-x-0 flex flex-col transition-transform duration-300 ease-in-out shadow-lg lg:shadow-none">
    
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-slate-200/80 dark:border-zinc-800/80 gap-3">
        <!-- <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-500 flex items-center justify-center shadow-md shadow-indigo-500/20 text-white">
            <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
        </div> -->
        <div class="w-full text-center">
            <span class="font-outfit font-bold text-lg text-black dark:text-white">
                TaskManager
            </span>
        </div>
    </div>

    <!-- Current User Brief -->
    <!-- <div class="p-6 border-b border-slate-100 dark:border-zinc-800/40">
        <div class="flex items-center gap-4">
            <div class="relative group">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop" 
                     alt="Admin Avatar" class="h-11 w-11 rounded-full object-cover ring-2 ring-indigo-500/20 dark:ring-indigo-500/40 group-hover:scale-105 transition duration-300">
                <span class="absolute bottom-0 right-0 h-3 w-3 bg-emerald-500 border-2 border-white dark:border-zinc-900 rounded-full"></span>
            </div>
            <div>
                <h4 class="font-semibold text-sm leading-tight">Sarah Connor</h4>
                <p class="text-xs text-slate-400 dark:text-zinc-500">Super Administrator</p>
            </div>
        </div>
    </div> -->

    <!-- Sidebar Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
        <a href="{{ route('dashboard') }}" id="nav-overview"
           class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
           {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800/60 dark:hover:text-zinc-100' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
            </svg>
            <span>Dashboard Overview</span>
        </a>

        <a href="{{ route('tasks.index') }}" id="nav-tasks"
           class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
           {{ request()->routeIs('tasks*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800/60 dark:hover:text-zinc-100' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span>Task</span>
        </a>

        <a href="{{ route('notes.index') }}" id="nav-notes"
           class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
           {{ request()->routeIs('notes*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800/60 dark:hover:text-zinc-100' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Notes</span>
        </a>

        <a href="{{ route('diff.index') }}" id="nav-diff"
           class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
           {{ request()->routeIs('diff.index') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800/60 dark:hover:text-zinc-100' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
            </svg>
            <span>Diff Checker</span>
        </a>

        <a href="{{ route('prettifier.index') }}" id="nav-prettifier"
           class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
           {{ request()->routeIs('prettifier.index') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800/60 dark:hover:text-zinc-100' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
            <span>Prettifier</span>
        </a>
    </nav>

    <!-- Footer version info -->
    <!-- <div class="p-6 border-t border-slate-100 dark:border-zinc-800/40 text-xs text-slate-400 dark:text-zinc-500">
        <div class="flex items-center justify-between mb-1">
            <span>Core Framework</span>
            <span class="font-semibold text-slate-600 dark:text-zinc-400">Laravel v13.8</span>
        </div>
        <div class="flex items-center justify-between">
            <span>Vite Compiler</span>
            <span class="font-semibold text-slate-600 dark:text-zinc-400">Vite v8.0</span>
        </div>
    </div> -->
</aside>
