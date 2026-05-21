@extends('layouts.app')

@section('title', 'Task | TaskManager')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit font-extrabold text-2xl md:text-3xl tracking-tight">Task</h1>
            <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Kelola semua tugas beserta sub-detail pekerjaan secara terstruktur.</p>
        </div>
        <a href="{{ route('tasks.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:from-violet-700 hover:to-indigo-700 transition shadow-md shadow-indigo-600/20 shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Task Baru</span>
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 rounded-2xl px-4 py-3 text-xs font-semibold">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Search & Filter Bar --}}
    <form method="GET" action="{{ route('tasks.index') }}" class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/60 dark:border-zinc-800/60 p-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul, catatan, atau user..."
                   class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none placeholder-slate-400 dark:placeholder-zinc-500">
        </div>
        @if($users->isNotEmpty())
        <select name="user"
                class="px-4 py-2 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <option value="">Semua User</option>
            @foreach($users as $u)
            <option value="{{ $u }}" @selected(request('user') === $u)>{{ $u }}</option>
            @endforeach
        </select>
        @endif
        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'user']))
            <a href="{{ route('tasks.index') }}"
               class="px-4 py-2 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 font-bold text-xs rounded-xl transition">
                Reset
            </a>
            @endif
        </div>
    </form>

    {{-- Task List --}}
    @if($tasks->isEmpty())
    <div class="bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-800/60 rounded-3xl p-16 text-center">
        <div class="h-16 w-16 bg-indigo-50 dark:bg-indigo-950/40 rounded-2xl flex items-center justify-center mx-auto mb-4 text-indigo-500">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <h3 class="font-outfit font-extrabold text-lg">Belum ada task</h3>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Klik tombol "Tambah Task Baru" untuk memulai.</p>
        <a href="{{ route('tasks.create') }}" class="inline-flex mt-5 items-center gap-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:from-violet-700 hover:to-indigo-700 transition shadow-md shadow-indigo-600/20">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Task Pertama
        </a>
    </div>
    @else
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100 dark:border-zinc-800/60 text-[10px] font-extrabold text-slate-400 dark:text-zinc-500 uppercase tracking-wider bg-slate-50/60 dark:bg-zinc-900/50">
                    <th class="px-6 py-4">Judul Task</th>
                    <th class="px-6 py-4 hidden md:table-cell">Tanggal</th>
                    <th class="px-6 py-4 hidden md:table-cell">User</th>
                    <th class="px-6 py-4 hidden lg:table-cell">Complated File</th>
                    <th class="px-6 py-4 hidden lg:table-cell">Path / Link</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/40 text-xs">
                @foreach($tasks as $task)
                <tr class="hover:bg-slate-50/60 dark:hover:bg-zinc-800/10 transition-colors">
                    <td class="px-6 py-4">
                        <a href="{{ route('tasks.show', $task) }}" class="font-bold text-slate-800 dark:text-zinc-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors line-clamp-1">
                            {{ $task->title }}
                        </a>
                        @if($task->note)
                        <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5 line-clamp-1">{{ $task->note }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-slate-500 dark:text-zinc-400 font-semibold">
                        {{ $task->date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        @if($task->user)
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-600 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 px-2.5 py-1 rounded-full">
                            <svg class="w-3 h-3 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ $task->user }}
                        </span>
                        @else
                        <span class="text-[11px] text-slate-400 dark:text-zinc-600">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        @php
                            $total     = $task->details_count;
                            $completed = $task->completed_count;
                            $pct       = $total > 0 ? round(($completed / $total) * 100) : 0;
                        @endphp
                        @if($total > 0)
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold {{ $pct === 100 ? 'text-emerald-500' : 'text-slate-600 dark:text-zinc-300' }}">
                                    {{ $completed }}/{{ $total }}
                                </span>
                                <span class="text-[10px] font-bold {{ $pct === 100 ? 'text-emerald-400' : 'text-slate-400 dark:text-zinc-500' }}">
                                    {{ $pct }}%
                                </span>
                            </div>
                            <div class="w-24 bg-slate-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $pct === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                        @else
                        <span class="text-[11px] text-slate-400 dark:text-zinc-600">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        @if($task->path)
                        <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-semibold line-clamp-1 max-w-[160px]">{{ $task->path }}</span>
                        @else
                        <span class="text-[11px] text-slate-400 dark:text-zinc-600">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('tasks.show', $task) }}"
                               class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 text-slate-400 hover:text-indigo-500 transition" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('tasks.edit', $task) }}"
                               class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 text-slate-400 hover:text-amber-500 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                  onsubmit="return confirm('Hapus task \'{{ addslashes($task->title) }}\' beserta semua sub-tugasnya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 text-slate-400 hover:text-rose-500 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($tasks->hasPages())
    <div class="flex justify-center">
        {{ $tasks->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
