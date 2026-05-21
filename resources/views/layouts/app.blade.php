<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#4f46e5">
    <title>@yield('title', 'Admin Dashboard | TaskManager')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS v4 Browser CDN for quick load, fallback to Laravel Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
        <style type="text/tailwindcss">
            @custom-variant dark (&:where(.dark, .dark *));
        </style>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
        }
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #3f3f46;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    <script>
        // Init dark mode immediately to prevent flicker
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-zinc-950 text-slate-800 dark:text-zinc-100 transition-colors duration-300 antialiased min-h-screen flex">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden hidden opacity-0 transition-opacity duration-300" onclick="toggleSidebar()"></div>

    <!-- Sidebar Container (Sidebar Partials) -->
    @include('layouts.partials.sidebar')

    <!-- Main Workspace -->
    <div id="mainWorkspace" class="flex-1 flex flex-col min-w-0 lg:pl-72 transition-all duration-300 ease-in-out">
        
        <!-- Navbar Header (Navbar Partials) -->
        @include('layouts.partials.navbar')

        <!-- Main Dynamic Content Wrapper -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            @yield('content')
        </main>
    </div>

    <!-- WIP Toast Alert System -->
    <div id="toastAlert" class="fixed bottom-6 right-6 z-50 max-w-sm bg-zinc-900 text-white rounded-2xl p-4 shadow-2xl flex items-center gap-3 border border-zinc-800 transform translate-y-24 opacity-0 transition-all duration-300">
        <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center shrink-0">
            <svg class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <div>
            <h5 class="font-bold text-xs">Simulated Admin Action</h5>
            <p id="toastMessage" class="text-[11px] text-zinc-400 mt-0.5">This action has been successfully triggered.</p>
        </div>
    </div>

    <!-- MODAL: SPOTLIGHT COMMAND PALETTE SEARCH -->
    <div id="spotlightModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" onclick="toggleSpotlightSearch(false)">
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-3xl w-full max-w-lg shadow-2xl mt-16 overflow-hidden transform scale-95 transition-transform duration-300" onclick="event.stopPropagation()">
            
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="spotlightInput" oninput="searchSpotlight()" placeholder="Search everything... (type 'todo', 'users', 'theme')" 
                       class="w-full pl-12 pr-6 py-4 text-xs bg-transparent border-b border-slate-100 dark:border-zinc-800/40 focus:outline-none text-slate-800 dark:text-zinc-100">
            </div>

            <!-- Suggested queries list -->
            <div id="spotlightResults" class="p-3.5 max-h-60 overflow-y-auto divide-y divide-slate-50 dark:divide-zinc-800/20 text-xs">
                
                <div onclick="executeSpotlight('switch:overview')" class="spot-item p-3 hover:bg-slate-50 dark:hover:bg-zinc-800/40 rounded-xl flex items-center justify-between cursor-pointer group transition">
                    <div class="flex items-center gap-3">
                        <span class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-zinc-200">Go to Overview</p>
                            <p class="text-[10px] text-slate-400 dark:text-zinc-500">Access main visual admin indicators</p>
                        </div>
                    </div>
                    <span class="text-[10px] bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded text-slate-500 font-bold uppercase">Navigation</span>
                </div>

                <div onclick="executeSpotlight('switch:todos')" class="spot-item p-3 hover:bg-slate-50 dark:hover:bg-zinc-800/40 rounded-xl flex items-center justify-between cursor-pointer group transition">
                    <div class="flex items-center gap-3">
                        <span class="h-8 w-8 rounded-lg bg-violet-50 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-zinc-200">Go to Task Manager</p>
                            <p class="text-[10px] text-slate-400 dark:text-zinc-500">Review task dashboard items & logs</p>
                        </div>
                    </div>
                    <span class="text-[10px] bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded text-slate-500 font-bold uppercase">Navigation</span>
                </div>

                <div onclick="executeSpotlight('switch:users')" class="spot-item p-3 hover:bg-slate-50 dark:hover:bg-zinc-800/40 rounded-xl flex items-center justify-between cursor-pointer group transition">
                    <div class="flex items-center gap-3">
                        <span class="h-8 w-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-zinc-200">Go to User Directory</p>
                            <p class="text-[10px] text-slate-400 dark:text-zinc-500">Search and edit database member access</p>
                        </div>
                    </div>
                    <span class="text-[10px] bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded text-slate-500 font-bold uppercase">Navigation</span>
                </div>

                <div onclick="executeSpotlight('theme')" class="spot-item p-3 hover:bg-slate-50 dark:hover:bg-zinc-800/40 rounded-xl flex items-center justify-between cursor-pointer group transition">
                    <div class="flex items-center gap-3">
                        <span class="h-8 w-8 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-3.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-zinc-200">Toggle Theme Mode</p>
                            <p class="text-[10px] text-slate-400 dark:text-zinc-500">Switch current UI dark/light theme instantly</p>
                        </div>
                    </div>
                    <span class="text-[10px] bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded text-slate-500 font-bold uppercase">System Action</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Javascripts for Layout Controllers -->
    <script>
        // Global State
        let currentTab = 'overview';

        // --- 1. THEME CONTROLLER ---
        function toggleDarkMode() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // --- 2. SIDEBAR NAVIGATION CONTROLLER ---
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const workspace = document.getElementById('mainWorkspace');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            const isDesktop = window.innerWidth >= 1024;
            
            if (isDesktop) {
                const isCollapsed = sidebar.classList.contains('lg:-translate-x-full');
                if (isCollapsed) {
                    sidebar.classList.remove('lg:-translate-x-full');
                    sidebar.classList.add('lg:translate-x-0');
                    workspace.classList.remove('lg:pl-0');
                    workspace.classList.add('lg:pl-72');
                } else {
                    sidebar.classList.add('lg:-translate-x-full');
                    sidebar.classList.remove('lg:translate-x-0');
                    workspace.classList.remove('lg:pl-72');
                    workspace.classList.add('lg:pl-0');
                }
            } else {
                const isClosed = sidebar.classList.contains('-translate-x-full');
                if (isClosed) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    backdrop.classList.remove('hidden');
                    setTimeout(() => backdrop.classList.add('opacity-100'), 20);
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                    backdrop.classList.remove('opacity-100');
                    setTimeout(() => backdrop.classList.add('hidden'), 300);
                }
            }
        }

        // Navigation Tabs Switcher
        function switchTab(tabId) {
            currentTab = tabId;
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            
            // Show target tab pane
            const targetPane = document.getElementById(`tab-${tabId}`);
            if (targetPane) {
                targetPane.classList.remove('hidden');
            }

            // Remove active classes from navigation buttons
            document.querySelectorAll('.nav-item').forEach(el => {
                el.classList.remove('bg-indigo-50', 'text-indigo-600', 'dark:bg-indigo-950/40', 'dark:text-indigo-400');
                el.classList.add('text-slate-500', 'hover:bg-slate-100', 'hover:text-slate-900', 'dark:text-zinc-400', 'dark:hover:bg-zinc-800/60', 'dark:hover:text-zinc-100');
            });

            // Set active class on current nav button
            const targetNav = document.getElementById(`nav-${tabId}`);
            if (targetNav) {
                targetNav.classList.add('bg-indigo-50', 'text-indigo-600', 'dark:bg-indigo-950/40', 'dark:text-indigo-400');
                targetNav.classList.remove('text-slate-500', 'hover:bg-slate-100', 'hover:text-slate-900', 'dark:text-zinc-400', 'dark:hover:bg-zinc-800/60', 'dark:hover:text-zinc-100');
            }

            // Close mobile sidebar if open
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar.classList.contains('-translate-x-full')) {
                    toggleSidebar();
                }
            }
        }

        // --- 3. TOP NAVBAR MENUS (DROPDOWNS & POPOVERS) ---
        function toggleProfileMenu(e) {
            e.stopPropagation();
            const menu = document.getElementById('profileMenu');
            const isHidden = menu.classList.contains('hidden');
            
            closeAllMenus();
            
            if (isHidden) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    menu.classList.remove('scale-95', 'opacity-0');
                }, 10);
            }
        }

        function toggleNotificationsMenu(e) {
            e.stopPropagation();
            const menu = document.getElementById('notificationsMenu');
            const isHidden = menu.classList.contains('hidden');
            
            closeAllMenus();
            
            if (isHidden) {
                menu.classList.remove('hidden');
                document.getElementById('notif-dot').classList.add('hidden'); // Clear alert dot
                setTimeout(() => {
                    menu.classList.remove('scale-95', 'opacity-0');
                }, 10);
            }
        }

        function closeAllMenus() {
            const pMenu = document.getElementById('profileMenu');
            const nMenu = document.getElementById('notificationsMenu');
            if (pMenu && !pMenu.classList.contains('hidden')) {
                pMenu.classList.add('scale-95', 'opacity-0');
                setTimeout(() => pMenu.classList.add('hidden'), 200);
            }
            if (nMenu && !nMenu.classList.contains('hidden')) {
                nMenu.classList.add('scale-95', 'opacity-0');
                setTimeout(() => nMenu.classList.add('hidden'), 200);
            }
        }

        document.addEventListener('click', closeAllMenus);

        function clearNotifications() {
            document.getElementById('notif-list').innerHTML = `
                <div class="p-8 text-center text-slate-400 dark:text-zinc-500">
                    <p class="font-semibold text-xs">No alerts found</p>
                    <p class="text-[10px] mt-0.5">System log is empty</p>
                </div>
            `;
            showToast('All notifications have been cleared.');
        }

        // Global Toast Controller
        function showToast(msg) {
            const toast = document.getElementById('toastAlert');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('translate-y-24', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 3500);
        }

        function showWIPAlert(moduleName) {
            showToast(`${moduleName} simulated action completed! Integration ready.`);
        }

        // --- 4. SPOTLIGHT SEARCH PALETTE ENGINE ---
        function toggleSpotlightSearch(show) {
            const modal = document.getElementById('spotlightModal');
            const content = modal.firstElementChild;
            const input = document.getElementById('spotlightInput');
            
            if (show) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    content.classList.remove('scale-95');
                    input.focus();
                }, 20);
            } else {
                modal.classList.add('opacity-0');
                content.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    input.value = '';
                    searchSpotlight(); // Reset results
                }, 200);
            }
        }

        // Global shortcut bindings
        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const modal = document.getElementById('spotlightModal');
                if (modal.classList.contains('hidden')) {
                    toggleSpotlightSearch(true);
                } else {
                    toggleSpotlightSearch(false);
                }
            }
            if (e.key === 'Escape') {
                toggleSpotlightSearch(false);
                // Also close userModal if child template has userModal
                const userModal = document.getElementById('userModal');
                if (userModal && !userModal.classList.contains('hidden')) {
                    toggleUserModal(false);
                }
            }
        });

        function searchSpotlight() {
            const query = document.getElementById('spotlightInput').value.toLowerCase().trim();
            const items = document.querySelectorAll('.spot-item');
            
            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                if (text.includes(query)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        function executeSpotlight(action) {
            toggleSpotlightSearch(false);
            if (action.startsWith('switch:')) {
                const tab = action.split(':')[1];
                switchTab(tab);
                showToast(`Navigated to ${tab.toUpperCase()} pane.`);
            } else if (action === 'theme') {
                toggleDarkMode();
                showToast(`Dark / Light Mode theme state toggled.`);
            }
        }
    </script>

    <!-- Child View Scripts Target -->
    @stack('scripts')
</body>
</html>
