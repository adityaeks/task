@extends('layouts.app')

@section('title', 'TaskManager')

@section('content')
    <!-- TAB 1: OVERVIEW PANEL -->
    <div id="tab-overview" class="tab-pane space-y-6">
        
        <!-- Dashboard Welcome Banner -->
        <div class="relative bg-gradient-to-r from-indigo-900 via-indigo-950 to-zinc-950 rounded-3xl p-6 md:p-8 overflow-hidden shadow-lg border border-indigo-950/20">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-violet-500/20 via-transparent to-transparent"></div>
            <div class="relative z-10 max-w-xl">
                <span class="bg-indigo-500/20 text-indigo-300 text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-indigo-400/25">Control Panel Ready</span>
                <h1 class="text-2xl md:text-3xl font-outfit font-extrabold text-white mt-3.5 tracking-tight">Selamat Datang Kembali!</h1>
                <p class="text-xs md:text-sm text-indigo-200/80 mt-1.5 font-medium leading-relaxed">Kelola semua daftar tugas dan pantau pembaruan status sistem dalam satu portal responsif.</p>
            </div>
        </div>

        @if(isset($pinnedNotes) && !$pinnedNotes->isEmpty())
        <!-- Pinned Notes Widget -->
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h3 class="font-outfit font-extrabold text-base">Disematkan di Notes</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Catatan penting Anda yang disematkan</p>
                </div>
                <a href="{{ route('notes.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 transition">
                    Kelola Notes
                    <svg class="w-3.5 h-3.5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($pinnedNotes as $note)
                    @php
                        $isLightWhite = ($note->color === '#ffffff' || !$note->color);
                        $cardClasses = $isLightWhite 
                            ? 'bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 text-slate-800 dark:text-zinc-100'
                            : 'border border-transparent text-slate-900';
                        $cardStyle = $isLightWhite ? '' : 'background-color: ' . $note->color . ';';
                        
                        // Parse content
                        $rawContent = $note->content;
                        $lines = explode("\n", e($rawContent));
                        
                        if (!function_exists('parseMarkdownInlineDashboard')) {
                            function parseMarkdownInlineDashboard($text) {
                                $text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-extrabold">$1</strong>', $text);
                                $text = preg_replace('/\*(.*?)\*/', '<em class="italic">$1</em>', $text);
                                return $text;
                            }
                        }

                        $parsedLines = [];
                        foreach ($lines as $line) {
                            if (preg_match('/^\[\s\]\s(.*)/', $line, $m)) {
                                $parsedText = parseMarkdownInlineDashboard($m[1]);
                                $parsedLines[] = '<span class="flex items-center gap-1.5 py-0.5"><input type="checkbox" disabled class="rounded border-slate-300 dark:border-zinc-700 text-indigo-600 focus:ring-0 w-3.5 h-3.5 pointer-events-none"> <span class="' . ($isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800') . '">' . $parsedText . '</span></span>';
                            } 
                            elseif (preg_match('/^\[x\]\s(.*)/i', $line, $m)) {
                                $parsedText = parseMarkdownInlineDashboard($m[1]);
                                $parsedLines[] = '<span class="flex items-center gap-1.5 py-0.5 line-through opacity-60"><input type="checkbox" checked disabled class="rounded border-slate-300 dark:border-zinc-700 text-indigo-600 focus:ring-0 w-3.5 h-3.5 pointer-events-none"> <span class="' . ($isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800') . '">' . $parsedText . '</span></span>';
                            }
                            elseif (preg_match('/^-\s(.*)/', $line, $m)) {
                                $parsedText = parseMarkdownInlineDashboard($m[1]);
                                $parsedLines[] = '<span class="flex items-start gap-1.5 py-0.5"><span class="' . ($isLightWhite ? 'text-indigo-500' : 'text-indigo-900') . ' mt-1.5 shrink-0 w-1.5 h-1.5 rounded-full bg-current"></span> <span class="' . ($isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800') . '">' . $parsedText . '</span></span>';
                            }
                            else {
                                $parsedText = parseMarkdownInlineDashboard($line);
                                $parsedLines[] = '<div>' . ($parsedText === '' ? '&nbsp;' : $parsedText) . '</div>';
                            }
                        }
                        $contentHtml = implode('', $parsedLines);
                    @endphp
                    
                    <div id="pinned-note-card-{{ $note->id }}" data-id="{{ $note->id }}" data-title="{{ $note->title ?: 'Tanpa Judul' }}" data-color="{{ $note->color ?? '#ffffff' }}" data-raw-content="{{ $note->content }}"
                         class="note-card rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 relative group flex flex-col justify-between cursor-pointer {{ $cardClasses }}"
                         style="{{ $cardStyle }}"
                         onclick="openViewNoteModal({{ $note->id }})">
                        
                        <div>
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <h4 class="font-bold text-xs leading-snug truncate">
                                    {{ $note->title ?: 'Tanpa Judul' }}
                                </h4>
                                <span class="text-indigo-600 dark:text-indigo-400 p-1">
                                    <svg class="w-4 h-4 fill-current rotate-0" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="17" x2="12" y2="22"></line>
                                        <path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.24V5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v4.24c0 .43-.14.85-.4 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24Z"></path>
                                    </svg>
                                </span>
                            </div>

                            {{-- Content --}}
                            <div class="text-[11px] font-medium leading-relaxed max-h-32 overflow-hidden">
                                {!! $contentHtml !!}
                            </div>
                        </div>

                        {{-- Footer Badge --}}
                        <div class="mt-4 flex items-center justify-between text-[10px] {{ $isLightWhite ? 'text-slate-400 dark:text-zinc-500' : 'text-slate-800/60' }} font-bold">
                            <span>Semat</span>
                            <span class="opacity-0 group-hover:opacity-100 transition-opacity {{ $isLightWhite ? 'text-indigo-600 dark:text-indigo-400' : 'text-indigo-900' }} flex items-center gap-0.5">
                                Buka
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Tasks Table -->
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-800/60 flex items-center justify-between">
                <div>
                    <h3 class="font-outfit font-extrabold text-base">Recent Tasks</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">5 task terbaru yang ditambahkan</p>
                </div>
                <a href="{{ route('tasks.create') }}"
                   class="px-4 py-2 text-xs font-bold rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white hover:from-violet-700 hover:to-indigo-700 transition shadow shadow-indigo-600/20">
                    + Tambah Task
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-zinc-800">
                            <th class="text-left px-6 py-3 font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Judul Task</th>
                            <th class="text-left px-4 py-3 font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">User</th>
                            <th class="text-left px-4 py-3 font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Tanggal</th>
                            <th class="text-left px-4 py-3 font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">File</th>
                            <th class="text-left px-4 py-3 font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Progress</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-zinc-800/60">
                        @forelse ($recentTasks as $task)
                        <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/40 transition">
                            <td class="px-6 py-3.5">
                                <p class="font-semibold text-slate-800 dark:text-zinc-200 truncate max-w-[200px]">{{ $task->title }}</p>
                                @if($task->path)
                                <p class="text-[10px] text-slate-400 dark:text-zinc-500 truncate max-w-[200px] mt-0.5">{{ $task->path }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold text-[10px] capitalize">
                                    {{ $task->user ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-500 dark:text-zinc-400 font-medium">
                                {{ $task->date ? $task->date->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-700 dark:text-zinc-300">{{ $task->details_count }}</span>
                                <span class="text-slate-400"> file</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @php
                                    $pct = $task->details_count > 0
                                        ? round(($task->completed_count / $task->details_count) * 100)
                                        : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-slate-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold {{ $pct === 100 ? 'text-emerald-500' : 'text-slate-400 dark:text-zinc-500' }}">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <a href="{{ route('tasks.show', $task) }}"
                                   class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400 dark:text-zinc-500 text-xs">
                                Belum ada task. <a href="{{ route('tasks.create') }}" class="text-indigo-500 font-bold hover:underline">Buat task pertama →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- TAB 2: TASK MANAGER PANEL (TODO LIST) -->
    <div id="tab-todos" class="tab-pane space-y-6 hidden">
        
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-outfit font-extrabold text-xl md:text-2xl">Task Administrator</h2>
                <p class="text-xs text-slate-400 dark:text-zinc-500">Interactive todo widget simulating client tasks storing dynamically to localStorage</p>
            </div>
            <button onclick="clearCompletedTodos()" class="text-xs font-bold text-rose-500 border border-rose-500/20 px-3 py-1.5 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/20 transition">
                Clear Completed
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- Form input task -->
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm space-y-4">
                <h3 class="font-outfit font-extrabold text-base">Register New Task</h3>
                <form id="todoForm" onsubmit="addTodo(event)" class="space-y-4.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-zinc-500 mb-1.5">Task Headline</label>
                        <input type="text" id="todoInput" required placeholder="e.g. Implement user login routes" 
                               class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-zinc-500 mb-1.5">Priority Level</label>
                        <select id="todoPriority" class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="low">🟢 Low Priority</option>
                            <option value="medium" selected>🟡 Medium Priority</option>
                            <option value="high">🔴 High Priority</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-zinc-500 mb-1.5">Category</label>
                        <input type="text" id="todoCategory" placeholder="e.g. Backend, Frontend, Docs" 
                               class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400 dark:placeholder-zinc-500">
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold text-xs py-3 rounded-xl hover:from-violet-700 hover:to-indigo-700 transition shadow-md shadow-indigo-600/10">
                        Create Task Entry
                    </button>
                </form>
            </div>

            <!-- Tasks display column -->
            <div class="lg:col-span-2 space-y-3.5">
                
                <!-- Mini filter bar -->
                <div class="flex items-center justify-between text-xs font-bold px-1 text-slate-400">
                    <span id="todoSummaryLabel">Showing 0 active tasks</span>
                    <div class="flex gap-2">
                        <button onclick="filterTodos('all')" id="filter-all" class="text-indigo-600 dark:text-indigo-400">All</button>
                        <span>•</span>
                        <button onclick="filterTodos('pending')" id="filter-pending">Pending</button>
                        <span>•</span>
                        <button onclick="filterTodos('completed')" id="filter-completed">Completed</button>
                    </div>
                </div>

                <!-- Dynamic Tasks Container -->
                <div id="todoList" class="space-y-3.5">
                    <!-- Injected dynamically via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: USER DIRECTORY PANEL -->
    <div id="tab-users" class="tab-pane space-y-6 hidden">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-outfit font-extrabold text-xl md:text-2xl">User Directory</h2>
                <p class="text-xs text-slate-400 dark:text-zinc-500">Comprehensive list of users with filtering, sorting and status updates</p>
            </div>
            <button onclick="toggleUserModal(true)" class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:from-violet-700 hover:to-indigo-700 transition shadow-md flex items-center gap-2 shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Add New Profile</span>
            </button>
        </div>

        <!-- Directory Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm overflow-hidden flex flex-col">
            
            <!-- Search & Filter bar -->
            <div class="p-4 md:p-6 border-b border-slate-100 dark:border-zinc-800/40 flex flex-col md:flex-row md:items-center gap-4 justify-between bg-slate-50/50 dark:bg-zinc-900/30">
                <div class="relative flex-1 max-w-sm">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="userSearchInput" oninput="renderUsersTable()" placeholder="Search users by name or email..." 
                           class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700/80 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                </div>
                
                <div class="flex items-center gap-3">
                    <select id="userFilterRole" onchange="renderUsersTable()" class="px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700/80 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                        <option value="all">All Roles</option>
                        <option value="Admin">Admin</option>
                        <option value="Editor">Editor</option>
                        <option value="User">User</option>
                    </select>
                    <select id="userFilterStatus" onchange="renderUsersTable()" class="px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700/80 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                        <option value="all">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <!-- Table responsive container -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-zinc-800/40 text-[10px] font-extrabold text-slate-400 dark:text-zinc-500 uppercase tracking-wider bg-slate-50/20 dark:bg-zinc-900/10">
                            <th class="px-6 py-4">User Details</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Registration Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody" class="divide-y divide-slate-100 dark:divide-zinc-800/40 text-xs">
                        <!-- Filled Dynamically via JS -->
                    </tbody>
                </table>
                
                <!-- Empty Search State -->
                <div id="userEmptyState" class="p-12 text-center hidden">
                    <div class="h-10 w-10 bg-slate-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-sm">No profiles match filters</h4>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Try resetting the input parameters.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD USER DIALOG -->
    <div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-3xl w-full max-w-md shadow-2xl p-6 transform scale-95 transition-transform duration-300">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/40 pb-3.5 mb-4.5">
                <h3 class="font-outfit font-extrabold text-base text-slate-800 dark:text-zinc-100">Create New Profile</h3>
                <button onclick="toggleUserModal(false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form onsubmit="createNewUser(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 dark:text-zinc-500 mb-1.5">Profile Full Name</label>
                    <input type="text" id="modalUserName" required placeholder="e.g. John Connor" 
                           class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 dark:text-zinc-500 mb-1.5">Email Address</label>
                    <input type="email" id="modalUserEmail" required placeholder="e.g. john@skynet.com" 
                           class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-zinc-500 mb-1.5">Role Position</label>
                        <select id="modalUserRole" class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="Admin">Admin</option>
                            <option value="Editor">Editor</option>
                            <option value="User" selected>User</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-zinc-500 mb-1.5">Status Option</label>
                        <select id="modalUserStatus" class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="Active" selected>Active</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="toggleUserModal(false)" class="flex-1 bg-slate-100 hover:bg-slate-200 dark:bg-zinc-800 dark:hover:bg-zinc-800/80 text-slate-700 dark:text-zinc-200 font-bold text-xs py-3 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold text-xs py-3 rounded-xl hover:from-violet-700 hover:to-indigo-700 transition shadow-md shadow-indigo-600/10">
                        Register Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: VIEW NOTE DIALOG -->
    <div id="view-note-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div id="view-modal-card" class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-3xl w-full max-w-md shadow-2xl p-6 transform scale-95 transition-transform duration-300 flex flex-col space-y-4">
            <div id="view-modal-header" class="flex items-start justify-between gap-4 pb-2 border-b border-slate-100 dark:border-zinc-800/40">
                <h3 id="view-modal-title" class="font-outfit font-extrabold text-lg leading-snug"></h3>
                <span id="view-modal-pin-icon" class="text-indigo-600 dark:text-indigo-400 p-1 shrink-0">
                    <svg class="w-4 h-4 fill-current rotate-0" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="17" x2="12" y2="22"></line>
                        <path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.24V5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1-1v4.24c0 .43-.14.85-.4 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24Z"></path>
                    </svg>
                </span>
            </div>
            
            <div id="view-modal-content" class="text-xs font-semibold leading-relaxed max-h-[50vh] overflow-y-auto pr-1">
                <!-- Content will be injected dynamically -->
            </div>
            
            <div id="view-modal-footer" class="pt-3 border-t border-slate-100 dark:border-zinc-800/40 flex items-center justify-between">
                <a href="{{ route('notes.index') }}" id="view-modal-edit-link" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                    Edit di Notes
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
                <button type="button" onclick="closeViewNoteModal()" id="view-modal-close-btn" class="px-4 py-2 text-xs font-bold rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // --- 1. MOCK DATA INITIALIZATION ---
        const defaultUsers = [
            { id: 1, name: "Sarah Connor", email: "sarah@skynet.net", role: "Admin", status: "Active", joined: "12 May 2026", avatar: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=128&auto=format&fit=crop" },
            { id: 2, name: "John Connor", email: "john@cyberdyne.org", role: "Editor", status: "Active", joined: "14 May 2026", avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=128&auto=format&fit=crop" },
            { id: 3, name: "Kyle Reese", email: "kyle@resistance.net", role: "User", status: "Active", joined: "15 May 2026", avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=128&auto=format&fit=crop" },
            { id: 4, name: "T-800 Model 101", email: "terminator@cyberdyne.org", role: "User", status: "Suspended", joined: "01 May 2026", avatar: "https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=128&auto=format&fit=crop" },
            { id: 5, name: "Marcus Wright", email: "marcus@projectangel.com", role: "User", status: "Active", joined: "18 May 2026", avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=128&auto=format&fit=crop" },
            { id: 6, name: "Katherine Brewster", email: "kate@defense.gov", role: "Editor", status: "Active", joined: "20 May 2026", avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=128&auto=format&fit=crop" }
        ];

        const defaultTodos = [
            { id: 1, text: "Optimize SQLite database indexes", priority: "high", category: "Backend", completed: true },
            { id: 2, text: "Build glassmorphic custom layout components", priority: "medium", category: "Frontend", completed: false },
            { id: 3, text: "Document Vite compilation workflows", priority: "low", category: "Docs", completed: false }
        ];

        // Hydrate localStorage if empty
        if (!localStorage.getItem('users_db')) {
            localStorage.setItem('users_db', JSON.stringify(defaultUsers));
        }
        if (!localStorage.getItem('todos_db')) {
            localStorage.setItem('todos_db', JSON.stringify(defaultTodos));
        }

        // Page level state
        let todoFilter = 'all';

        // --- 2. TASK MANAGER LOGIC (TODO CRUD) ---
        function getTodos() {
            return JSON.parse(localStorage.getItem('todos_db')) || [];
        }

        function saveTodos(todos) {
            localStorage.setItem('todos_db', JSON.stringify(todos));
            updateMetrics();
            renderTodos();
        }

        function addTodo(e) {
            e.preventDefault();
            const input = document.getElementById('todoInput');
            const priority = document.getElementById('todoPriority').value;
            const category = document.getElementById('todoCategory').value || 'General';
            
            const todos = getTodos();
            const newTodo = {
                id: Date.now(),
                text: input.value.trim(),
                priority,
                category,
                completed: false
            };
            
            todos.push(newTodo);
            saveTodos(todos);
            
            input.value = '';
            document.getElementById('todoCategory').value = '';
            showToast(`Added new task: "${newTodo.text}"`);
        }

        function toggleTodo(id) {
            const todos = getTodos();
            const todo = todos.find(t => t.id === id);
            if (todo) {
                todo.completed = !todo.completed;
                saveTodos(todos);
                showToast(todo.completed ? `Completed: "${todo.text}"` : `Reopened: "${todo.text}"`);
            }
        }

        function deleteTodo(id) {
            const todos = getTodos();
            const filtered = todos.filter(t => t.id !== id);
            saveTodos(filtered);
            showToast("Task item removed permanently.");
        }

        function clearCompletedTodos() {
            const todos = getTodos();
            const pending = todos.filter(t => !t.completed);
            if (pending.length === todos.length) {
                showToast("No completed tasks to clear.");
                return;
            }
            saveTodos(pending);
            showToast("Cleared completed tasks from list.");
        }

        function filterTodos(mode) {
            todoFilter = mode;
            document.querySelectorAll('#filter-all, #filter-pending, #filter-completed').forEach(btn => {
                btn.classList.remove('text-indigo-600', 'dark:text-indigo-400');
            });
            document.getElementById(`filter-${mode}`).classList.add('text-indigo-600', 'dark:text-indigo-400');
            renderTodos();
        }

        function renderTodos() {
            const todos = getTodos();
            const container = document.getElementById('todoList');
            container.innerHTML = '';
            
            const filtered = todos.filter(t => {
                if (todoFilter === 'pending') return !t.completed;
                if (todoFilter === 'completed') return t.completed;
                return true;
            });

            document.getElementById('todoSummaryLabel').innerText = `Showing ${filtered.length} tasks`;
            
            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/50 rounded-3xl p-12 text-center">
                        <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-950/40 rounded-full flex items-center justify-center mx-auto text-indigo-500 mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm">No tasks matched search</h4>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-0.5">Use the form on the left to add items.</p>
                    </div>
                `;
                return;
            }

            filtered.forEach(todo => {
                const priorityBadge = todo.priority === 'high' ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/20 dark:text-rose-400' :
                                      todo.priority === 'medium' ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400' :
                                      'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400';
                
                const item = document.createElement('div');
                item.className = `flex items-center justify-between p-4 bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-800/60 rounded-2xl shadow-xs transition duration-200 hover:border-slate-300 dark:hover:border-zinc-700/60 ${todo.completed ? 'opacity-70' : ''}`;
                
                item.innerHTML = `
                    <div class="flex items-center gap-4.5 min-w-0">
                        <button onclick="toggleTodo(${todo.id})" class="h-5.5 w-5.5 rounded-lg border border-slate-300 dark:border-zinc-700 flex items-center justify-center cursor-pointer transition ${todo.completed ? 'bg-indigo-500 border-indigo-500 text-white' : 'hover:border-indigo-500'}">
                            ${todo.completed ? `
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            ` : ''}
                        </button>
                        <div class="min-w-0">
                            <p class="font-semibold text-xs text-slate-800 dark:text-zinc-200 truncate ${todo.completed ? 'line-through text-slate-400 dark:text-zinc-500' : ''}">${escapeHtml(todo.text)}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] uppercase font-bold tracking-wider ${priorityBadge} px-2 py-0.5 rounded-md">${todo.priority}</span>
                                <span class="text-[10px] font-semibold text-slate-400 dark:text-zinc-500 bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">${escapeHtml(todo.category)}</span>
                            </div>
                        </div>
                    </div>
                    <button onclick="deleteTodo(${todo.id})" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-xl transition cursor-pointer">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                `;
                container.appendChild(item);
            });
        }

        // --- 3. USER DIRECTORY LOGIC ---
        function toggleUserModal(show) {
            const modal = document.getElementById('userModal');
            const content = modal.firstElementChild;
            if (show) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    content.classList.remove('scale-95');
                }, 20);
            } else {
                modal.classList.add('opacity-0');
                content.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }
        }

        function getUsers() {
            return JSON.parse(localStorage.getItem('users_db')) || [];
        }

        function saveUsers(users) {
            localStorage.setItem('users_db', JSON.stringify(users));
            updateMetrics();
            renderUsersTable();
        }

        function createNewUser(e) {
            e.preventDefault();
            const name = document.getElementById('modalUserName').value.trim();
            const email = document.getElementById('modalUserEmail').value.trim();
            const role = document.getElementById('modalUserRole').value;
            const status = document.getElementById('modalUserStatus').value;
            
            const users = getUsers();
            const newUser = {
                id: Date.now(),
                name,
                email,
                role,
                status,
                joined: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }),
                avatar: `https://images.unsplash.com/photo-${1500000000000 + Math.floor(Math.random() * 999999)}?q=80&w=128&auto=format&fit=crop`
            };

            users.push(newUser);
            saveUsers(users);
            
            toggleUserModal(false);
            e.target.reset();
            showToast(`Registered user: "${name}"`);
        }

        function toggleUserStatus(id) {
            const users = getUsers();
            const user = users.find(u => u.id === id);
            if (user) {
                user.status = user.status === 'Active' ? 'Suspended' : 'Active';
                saveUsers(users);
                showToast(`Status updated to ${user.status} for ${user.name}`);
            }
        }

        function deleteUser(id) {
            const users = getUsers();
            const user = users.find(u => u.id === id);
            if (user && confirm(`Remove profile for "${user.name}"?`)) {
                const filtered = users.filter(u => u.id !== id);
                saveUsers(filtered);
                showToast(`User profile removed.`);
            }
        }

        function renderUsersTable() {
            const users = getUsers();
            const search = document.getElementById('userSearchInput').value.toLowerCase().trim();
            const roleFilter = document.getElementById('userFilterRole').value;
            const statusFilter = document.getElementById('userFilterStatus').value;
            const tbody = document.getElementById('userTableBody');
            
            tbody.innerHTML = '';

            const filtered = users.filter(u => {
                const matchesSearch = u.name.toLowerCase().includes(search) || u.email.toLowerCase().includes(search);
                const matchesRole = roleFilter === 'all' || u.role === roleFilter;
                const matchesStatus = statusFilter === 'all' || u.status === statusFilter;
                return matchesSearch && matchesRole && matchesStatus;
            });

            if (filtered.length === 0) {
                document.getElementById('userEmptyState').classList.remove('hidden');
                return;
            } else {
                document.getElementById('userEmptyState').classList.add('hidden');
            }

            filtered.forEach(u => {
                const statusColor = u.status === 'Active' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400 border-emerald-500/10' :
                                                           'bg-rose-50 text-rose-600 dark:bg-rose-950/20 dark:text-rose-400 border-rose-500/10';
                
                const tr = document.createElement('tr');
                tr.className = "hover:bg-slate-50/50 dark:hover:bg-zinc-800/10 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3.5">
                            <img src="${u.avatar}" onerror="this.src='https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=128&auto=format&fit=crop'" alt="User Avatar" class="h-9 w-9 rounded-full object-cover">
                            <div>
                                <p class="font-bold text-xs text-slate-800 dark:text-zinc-200">${escapeHtml(u.name)}</p>
                                <p class="text-[10px] text-slate-400 dark:text-zinc-500">${escapeHtml(u.email)}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 font-semibold text-xs text-slate-600 dark:text-zinc-300">${escapeHtml(u.role)}</td>
                    <td class="px-6 py-3.5">
                        <button onclick="toggleUserStatus(${u.id})" class="px-2.5 py-0.5 rounded-full border text-[10px] font-extrabold uppercase select-none transition cursor-pointer ${statusColor}">
                            ${u.status}
                        </button>
                    </td>
                    <td class="px-6 py-3.5 text-slate-400 dark:text-zinc-500 font-semibold">${u.joined}</td>
                    <td class="px-6 py-3.5 text-right space-x-1">
                        <button onclick="showWIPAlert('Edit details for ' + '${escapeHtml(u.name)}')" class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button onclick="deleteUser(${u.id})" class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-lg text-slate-400 hover:text-rose-50 transition cursor-pointer">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" />
                            </svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // --- 4. DATA METRICS UPDATE CONTROLLER ---
        function updateMetrics() {
            const users = getUsers();
            const todos = getTodos();
            
            // Total Users Stat
            const statUsers = document.getElementById('stat-total-users');
            if (statUsers) statUsers.innerText = users.length;
            
            // Total Active Tasks
            const activeTodos = todos.filter(t => !t.completed).length;
            const statActiveTodos = document.getElementById('stat-active-todos');
            if (statActiveTodos) statActiveTodos.innerText = activeTodos;
            
            const statTodosMeta = document.getElementById('stat-todos-meta');
            if (statTodosMeta) statTodosMeta.innerText = `${todos.length} total tasks registered`;
            
            // Todo Sidebar Navigation Badge
            const badge = document.getElementById('badge-todo-count');
            if (badge) {
                badge.innerText = activeTodos;
                if (activeTodos > 0) {
                    badge.classList.remove('scale-0');
                } else {
                    badge.classList.add('scale-0');
                }
            }

            // Completion rate calculation
            const completedCount = todos.filter(t => t.completed).length;
            const rate = todos.length > 0 ? Math.round((completedCount / todos.length) * 100) : 0;
            
            const statRate = document.getElementById('stat-completion-rate');
            if (statRate) statRate.innerText = `${rate}%`;
            
            const statBar = document.getElementById('stat-completion-bar');
            if (statBar) statBar.style.width = `${rate}%`;
        }

        function escapeHtml(str) {
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // --- 5. INTERACTIVE SVG CHART HOVER SYSTEM ---
        function setupChartHover() {
            const chart = document.getElementById('activityChart');
            if (!chart) return;
            
            const line = document.getElementById('chartHoverLine');
            const point = document.getElementById('chartHoverPoint');
            const tooltip = document.getElementById('chartTooltip');
            const tooltipDay = document.getElementById('tooltipDay');
            const tooltipVal = document.getElementById('tooltipVal');
            
            const points = [
                { x: 10, y: 180, day: 'Mon', val: '12 tasks' },
                { x: 123, y: 80, day: 'Tue', val: '38 tasks' },
                { x: 230, y: 130, day: 'Wed', val: '25 tasks' },
                { x: 340, y: 100, day: 'Thu', val: '32 tasks' },
                { x: 450, y: 60, day: 'Fri', val: '45 tasks' },
                { x: 570, y: 120, day: 'Sat', val: '28 tasks' },
                { x: 690, y: 90, day: 'Sun', val: '35 tasks' }
            ];

            chart.addEventListener('mousemove', e => {
                const rect = chart.getBoundingClientRect();
                const mouseX = ((e.clientX - rect.left) / rect.width) * 700; // Map screen coordinates back to SVG bounds

                // Find closest data dot
                let closest = points[0];
                let minDist = Math.abs(mouseX - points[0].x);
                
                for(let i=1; i<points.length; i++) {
                    let d = Math.abs(mouseX - points[i].x);
                    if(d < minDist) {
                        minDist = d;
                        closest = points[i];
                    }
                }

                // Render indicator state
                line.setAttribute('x1', closest.x);
                line.setAttribute('x2', closest.x);
                line.classList.remove('hidden');

                point.setAttribute('cx', closest.x);
                point.setAttribute('cy', closest.y);
                point.classList.remove('hidden');

                // Float tooltip box
                const tooltipX = (closest.x / 700) * rect.width;
                const tooltipY = (closest.y / 220) * rect.height;

                tooltip.style.left = `${tooltipX}px`;
                tooltip.style.top = `${tooltipY}px`;
                tooltip.classList.remove('hidden');
                
                tooltipDay.innerText = closest.day;
                tooltipVal.innerText = closest.val;
            });

            chart.addEventListener('mouseleave', () => {
                line.classList.add('hidden');
                point.classList.add('hidden');
                tooltip.classList.add('hidden');
            });
        }

        // ══════════════════════════════════════ DASHBOARD VIEW NOTE MODAL ══════════════════════════════════════

        // JavaScript helper untuk parse bold dan italic inline markdown
        function parseMarkdownInlineDashboardJS(text) {
            if (!text) return '';
            let parsed = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-extrabold">$1</strong>');
            parsed = parsed.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
            return parsed;
        }

        // JavaScript parser untuk HTML render di card modal
        function parseContentToHtmlDashboard(content, isLightWhite) {
            if (!content) return '';
            const lines = content.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").split('\n');
            const parsedLines = lines.map(line => {
                let match;
                if ((match = line.match(/^\[\s\]\s(.*)/))) {
                    const txt = parseMarkdownInlineDashboardJS(match[1]);
                    return `<span class="flex items-center gap-1.5 py-0.5"><input type="checkbox" disabled class="rounded border-slate-300 dark:border-zinc-700 text-indigo-600 focus:ring-0 w-3.5 h-3.5 pointer-events-none"> <span class="${isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800'}">${txt}</span></span>`;
                } else if ((match = line.match(/^\[x\]\s(.*)/i))) {
                    const txt = parseMarkdownInlineDashboardJS(match[1]);
                    return `<span class="flex items-center gap-1.5 py-0.5 line-through opacity-60"><input type="checkbox" checked disabled class="rounded border-slate-300 dark:border-zinc-700 text-indigo-600 focus:ring-0 w-3.5 h-3.5 pointer-events-none"> <span class="${isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800'}">${txt}</span></span>`;
                } else if ((match = line.match(/^-\s(.*)/))) {
                    const txt = parseMarkdownInlineDashboardJS(match[1]);
                    return `<span class="flex items-start gap-1.5 py-0.5"><span class="${isLightWhite ? 'text-indigo-500' : 'text-indigo-900'} mt-1.5 shrink-0 w-1.5 h-1.5 rounded-full bg-current"></span> <span class="${isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800'}">${txt}</span></span>`;
                } else {
                    const txt = parseMarkdownInlineDashboardJS(line);
                    return `<div>${txt === '' ? '&nbsp;' : txt}</div>`;
                }
            });
            return parsedLines.join('');
        }

        function openViewNoteModal(id) {
            const card = document.getElementById(`pinned-note-card-${id}`);
            if (!card) return;

            const title = card.getAttribute('data-title') || 'Tanpa Judul';
            const rawContent = card.getAttribute('data-raw-content') || '';
            const color = card.getAttribute('data-color') || '#ffffff';
            
            const isWhite = (color === '#ffffff');

            const modal = document.getElementById('view-note-modal');
            const modalCard = document.getElementById('view-modal-card');
            const titleEl = document.getElementById('view-modal-title');
            const contentEl = document.getElementById('view-modal-content');
            const closeBtn = document.getElementById('view-modal-close-btn');
            
            const headerBorder = document.getElementById('view-modal-header');
            const footerBorder = document.getElementById('view-modal-footer');
            const editLink = document.getElementById('view-modal-edit-link');
            const pinIcon = document.getElementById('view-modal-pin-icon');

            // Parse HTML content
            contentEl.innerHTML = parseContentToHtmlDashboard(rawContent, isWhite);

            titleEl.textContent = title;
            
            if (isWhite) {
                modalCard.style.backgroundColor = '';
                modalCard.className = "bg-white dark:bg-zinc-900 w-full max-w-md rounded-3xl border border-slate-200 dark:border-zinc-800 shadow-2xl p-6 transform scale-95 transition-transform duration-300 flex flex-col space-y-4";
                titleEl.className = "font-outfit font-extrabold text-lg leading-snug text-slate-800 dark:text-zinc-100";
                
                headerBorder.className = "flex items-start justify-between gap-4 pb-2 border-b border-slate-100 dark:border-zinc-800/40";
                footerBorder.className = "pt-3 border-t border-slate-100 dark:border-zinc-800/40 flex items-center justify-between";
                editLink.className = "text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1";
                pinIcon.className = "text-indigo-600 dark:text-indigo-400 p-1 shrink-0";
                
                closeBtn.className = "px-4 py-2 text-xs font-bold rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 transition text-slate-700 dark:text-zinc-200";
            } else {
                modalCard.style.backgroundColor = color;
                modalCard.className = "w-full max-w-md rounded-3xl border border-transparent shadow-2xl p-6 transform scale-95 transition-transform duration-300 flex flex-col space-y-4 text-slate-900";
                titleEl.className = "font-outfit font-extrabold text-lg leading-snug text-slate-900";
                
                headerBorder.className = "flex items-start justify-between gap-4 pb-2 border-b border-slate-900/10";
                footerBorder.className = "pt-3 border-t border-slate-900/10 flex items-center justify-between";
                editLink.className = "text-xs font-bold text-indigo-900 hover:text-indigo-950 hover:underline flex items-center gap-1";
                pinIcon.className = "text-indigo-900 p-1 shrink-0";
                
                closeBtn.className = "px-4 py-2 text-xs font-bold rounded-xl bg-slate-900/10 hover:bg-slate-900/20 transition text-slate-900";
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalCard.classList.remove('scale-95');
            }, 20);
        }

        function closeViewNoteModal() {
            const modal = document.getElementById('view-note-modal');
            const modalCard = document.getElementById('view-modal-card');
            modal.classList.add('opacity-0');
            modalCard.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close view modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeViewNoteModal();
            }
        });

        // Close modal when clicking on backdrop
        document.getElementById('view-note-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeViewNoteModal();
            }
        });

        // Initialize lists on load
        window.addEventListener('DOMContentLoaded', () => {
            renderTodos();
            renderUsersTable();
            updateMetrics();
            setupChartHover();
        });
    </script>
@endpush
