<!-- Top Header Navigation -->
<header class="h-16 bg-white dark:bg-zinc-900 border-b border-slate-200/80 dark:border-zinc-800/80 px-6 flex items-center justify-between sticky top-0 z-30">
    
    <!-- Left Header: Hamburger & Search Input Mock -->
    <div class="flex items-center gap-4 flex-1">
        <button onclick="toggleSidebar()" class="text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 p-2 rounded-xl transition cursor-pointer" title="Toggle Sidebar">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <!-- <div class="relative w-80 max-w-xs md:max-w-md hidden sm:block">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" onclick="toggleSpotlightSearch(true)" placeholder="Search anything... (Ctrl+K)" 
                   class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800/60 border-0 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 cursor-pointer placeholder-slate-400 dark:placeholder-zinc-500">
        </div> -->
    </div>

    <!-- Right Header: Widgets -->
    <div class="flex items-center gap-2">
        
        <!-- Command Palette Shortcut Button -->
        <!-- <button onclick="toggleSpotlightSearch(true)" class="p-2.5 rounded-xl text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 transition-colors sm:hidden">
            <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button> -->

        <!-- Dark Mode Toggle -->
        <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 transition-all duration-300 relative overflow-hidden group" title="Toggle Theme">
            <svg id="sun-icon" class="w-5.5 h-5.5 transition-transform duration-300 scale-100 dark:scale-0 dark:absolute" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-3.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
            </svg>
            <svg id="moon-icon" class="w-5.5 h-5.5 transition-transform duration-300 scale-0 dark:scale-100 absolute dark:relative dark:left-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <!-- Notifications Popover Wrapper -->
        <!--<div class="relative">
            <button onclick="toggleNotificationsMenu(event)" class="p-2.5 rounded-xl text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 transition-colors relative">
                <svg class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span id="notif-dot" class="absolute top-2 right-2.5 h-2.5 w-2.5 bg-rose-500 border border-white dark:border-zinc-900 rounded-full animate-bounce"></span>
            </button>-->

            <!-- Notifications Dropdown Box -->
            <!-- <div id="notificationsMenu" class="absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 rounded-2xl shadow-xl py-2 z-50 hidden transition-all duration-200 origin-top-right scale-95 opacity-0">
                <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100 dark:border-zinc-800/40">
                    <span class="font-bold text-xs">Recent Alerts</span>
                    <button onclick="clearNotifications()" class="text-[10px] text-indigo-500 font-bold hover:underline">Clear all</button>
                </div>
                <div id="notif-list" class="max-h-72 overflow-y-auto divide-y divide-slate-50 dark:divide-zinc-800/40 text-xs">
                    <div class="p-3.5 hover:bg-slate-50 dark:hover:bg-zinc-800/20 flex gap-3 transition">
                        <span class="h-8 w-8 rounded-full bg-violet-100 dark:bg-violet-950/40 flex items-center justify-center text-violet-600 dark:text-violet-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold leading-tight">Server Uptime Spike</p>
                            <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">CPU load reached 88% 5 mins ago.</p>
                        </div>
                    </div>
                    <div class="p-3.5 hover:bg-slate-50 dark:hover:bg-zinc-800/20 flex gap-3 transition">
                        <span class="h-8 w-8 rounded-full bg-emerald-100 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold leading-tight">Backup Succeeded</p>
                            <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">Database snapshot stored safely.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Vertical Divider -->
        <div class="h-6 w-px bg-slate-200 dark:bg-zinc-800 mx-2"></div>

        <!-- User Dropdown Wrapper -->
        <!-- <div class="relative">
            <button onclick="toggleProfileMenu(event)" class="flex items-center gap-2 p-1 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl transition duration-150">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop" 
                     alt="Avatar" class="h-8 w-8 rounded-lg object-cover">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button> -->

            <!-- Profile Dropdown Content Box -->
            <!-- <div id="profileMenu" class="absolute right-0 mt-2 w-52 bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 rounded-2xl shadow-xl py-2 z-50 hidden transition-all duration-200 origin-top-right scale-95 opacity-0">
                <div class="px-4 py-2 border-b border-slate-100 dark:border-zinc-800/40 mb-1.5">
                    <p class="font-bold text-xs leading-none">Sarah Connor</p>
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500 mt-0.5">admin@taskmanager.io</p>
                </div>
                <button onclick="showWIPAlert('Profile Settings')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-zinc-800/40 flex items-center gap-2.5 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profile Details</span>
                </button>
                <button onclick="showWIPAlert('Settings Config')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-zinc-800/40 flex items-center gap-2.5 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                    <span>Preferences</span>
                </button>
                <div class="h-px bg-slate-100 dark:bg-zinc-800 my-1.5"></div>
                <a href="/" class="block px-4 py-2 text-xs text-rose-500 hover:bg-slate-50 dark:hover:bg-zinc-800/40 flex items-center gap-2.5 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Exit Portal</span>
                </a>
            </div>
        </div> -->
    </div>
</header>
