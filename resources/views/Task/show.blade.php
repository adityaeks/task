@extends('layouts.app')

@section('title', $task->title . ' | TaskManager')

@section('content')
<style>
    /* Failsafe iOS-Style Toggle Switch CSS to bypass Tailwind compilation on server */
    .status-toggle-label {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        cursor: pointer !important;
        user-select: none !important;
    }

    .status-toggle-input {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border-width: 0 !important;
    }

    .status-toggle-slider {
        position: relative !important;
        width: 32px !important;
        height: 18px !important;
        background-color: #cbd5e1 !important; /* light slate-300 */
        border-radius: 9999px !important;
        transition: background-color 0.2s ease, box-shadow 0.2s ease !important;
    }
    .dark .status-toggle-slider {
        background-color: #27272a !important; /* dark zinc-800 */
    }

    /* Toggle Knob using ::after pseudoclass */
    .status-toggle-slider::after {
        content: "" !important;
        position: absolute !important;
        top: 2px !important;
        left: 2px !important;
        background-color: #ffffff !important;
        border-radius: 50% !important;
        height: 14px !important;
        width: 14px !important;
        transition: transform 0.2s ease !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15) !important;
    }

    /* Active state when checkbox is checked */
    .status-toggle-input:checked + .status-toggle-slider {
        background-color: #10b981 !important; /* emerald-500 */
    }

    .status-toggle-input:checked + .status-toggle-slider::after {
        transform: translateX(14px) !important;
    }

    /* Focus Ring support */
    .status-toggle-input:focus-visible + .status-toggle-slider {
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.4) !important; /* indigo ring */
    }
</style>

<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-zinc-500">
        <a href="{{ route('tasks.index') }}" class="hover:text-indigo-500 transition font-semibold">Task Manager</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-600 dark:text-zinc-300 font-semibold truncate max-w-xs">{{ $task->title }}</span>
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

    {{-- Header Info Card --}}
    @php
        $detailsCount   = $task->details->count();
        $completedCount = $task->details->where('status', 'Completed')->count();
        $progressPct    = $detailsCount > 0 ? round(($completedCount / $detailsCount) * 100) : 0;
        $progressColor  = $progressPct === 100 ? '#10b981' : ($progressPct > 50 ? '#f59e0b' : '#6366f1');
    @endphp
    <div class="relative bg-gradient-to-br from-indigo-900 via-indigo-950 to-zinc-950 rounded-3xl p-6 md:p-8 overflow-hidden text-white shadow-lg">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(139,92,246,0.15),_transparent)]"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-3 flex-1">
                <h1 class="font-outfit font-extrabold text-2xl md:text-3xl tracking-tight leading-tight">{{ $task->title }}</h1>

                {{-- Meta info row --}}
                <div class="flex flex-wrap items-center gap-4 text-xs text-indigo-200/80 font-semibold">
                    {{-- Date --}}
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $task->date->format('d M Y') }}
                    </span>
                    {{-- User --}}
                    @if($task->user)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $task->user }}
                    </span>
                    @endif
                    {{-- Category --}}
                    @if($task->category)
                    <span class="inline-flex items-center gap-1 font-bold px-2 py-0.5 rounded-full text-[11px]
                        {{ $task->category === 'Live'
                            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                            : 'bg-violet-500/20 text-violet-300 border border-violet-500/30' }}">
                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $task->category === 'Live' ? 'bg-emerald-400' : 'bg-violet-400' }}"></span>
                        {{ $task->category }}
                    </span>
                    @endif
                    {{-- Sub-tasks count --}}
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        {{ $detailsCount }} Task Detail
                    </span>
                    {{-- Path/Link --}}
                    @if($task->path)
                    <span class="flex items-center gap-1.5 opacity-80 max-w-xs truncate">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <span class="truncate">{{ $task->path }}</span>
                    </span>
                    @endif
                </div>

                {{-- Note --}}
                @if($task->note)
                <p class="text-sm text-indigo-200/80 leading-relaxed max-w-xl">{{ $task->note }}</p>
                @endif
            </div>

            {{-- Progress Circle --}}
            <div class="flex flex-col items-center gap-2 shrink-0">
                <div class="relative h-20 w-20">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3"/>
                        <circle id="progress-circle" cx="18" cy="18" r="15.9" fill="none" stroke="white" stroke-width="3"
                                stroke-dasharray="{{ $progressPct }} {{ 100 - $progressPct }}"
                                stroke-linecap="round"
                                class="transition-all duration-500 ease-out"/>
                    </svg>
                    <span id="progress-text" class="absolute inset-0 flex items-center justify-center text-lg font-outfit font-extrabold text-white">{{ $progressPct }}%</span>
                </div>
                <p id="progress-subtext" class="text-[11px] font-bold text-white/60 text-center">{{ $completedCount }}/{{ $detailsCount }} selesai</p>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('tasks.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 font-bold text-xs rounded-xl transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('tasks.edit', $task) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-950/50 font-bold text-xs rounded-xl border border-amber-200 dark:border-amber-800/40 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit Task
            </a>
            <form id="delete-form-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" onclick="showConfirmDeleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/50 font-bold text-xs rounded-xl border border-rose-200 dark:border-rose-800/40 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Sub-Task Details Table --}}
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-800/40 bg-slate-50/60 dark:bg-zinc-900/30 flex items-center justify-between">
            <div>
                <h2 class="font-outfit font-extrabold text-base">Detail Task</h2>
                <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">Daftar pekerjaan rinci yang termasuk dalam task ini.</p>
            </div>
            @if($detailsCount > 0)
            <div class="flex items-center gap-2 text-xs font-bold">
                <span id="table-completed-count" class="text-emerald-600 dark:text-emerald-400">{{ $completedCount }}</span>
                <span class="text-slate-400">/</span>
                <span id="table-details-count" class="text-slate-600 dark:text-zinc-300">{{ $detailsCount }}</span>
                <span class="text-slate-400">selesai</span>
            </div>
            @endif
        </div>

        @if($task->details->isEmpty())
        <div class="p-12 text-center">
            <div class="h-12 w-12 bg-slate-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="font-bold text-sm">Belum ada sub-tugas</p>
            <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Edit task untuk menambahkan sub-tugas detail.</p>
            <a href="{{ route('tasks.edit', $task) }}" class="inline-flex mt-4 items-center gap-1.5 text-xs font-bold text-indigo-500 hover:text-indigo-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Sub-Tugas
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="text-[10px] font-extrabold text-slate-400 dark:text-zinc-500 uppercase tracking-wider border-b border-slate-100 dark:border-zinc-800/40">
                        <th class="px-6 py-3 w-8">#</th>
                        <th class="px-6 py-3">Nama File</th>
                        <th class="px-6 py-3 hidden md:table-cell">Deskripsi</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/40">
                    @foreach($task->details as $i => $detail)
                    @php $done = $detail->status === 'Completed'; @endphp
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/10 transition-colors">
                        <td class="px-6 py-3.5">
                            <span id="circle-{{ $detail->id }}" 
                                  data-index="{{ $i + 1 }}"
                                  class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-extrabold transition-all duration-200
                                      {{ $done ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500' }}">
                                @if($done)
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <span id="name-{{ $detail->id }}" class="font-semibold transition-all duration-200 {{ $done ? 'line-through text-slate-400 dark:text-zinc-500' : 'text-slate-800 dark:text-zinc-200' }}">
                                {{ $detail->name }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 hidden md:table-cell text-slate-500 dark:text-zinc-400 max-w-xs">
                            <span class="line-clamp-2">{{ $detail->desc ?: '—' }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            <label class="status-toggle-label">
                                <input type="checkbox" 
                                       id="toggle-{{ $detail->id }}"
                                       class="status-toggle-input subtask-toggle" 
                                       onchange="toggleSubtaskStatus({{ $detail->id }}, this)" 
                                       @checked($done)>
                                <div class="status-toggle-slider"></div>
                            </label>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

{{-- Custom Delete Confirmation Modal --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 dark:bg-black/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/80 dark:border-zinc-800/80 w-full max-w-sm overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="p-6 text-center space-y-4">
            <div class="h-12 w-12 bg-rose-50 dark:bg-rose-950/30 rounded-2xl flex items-center justify-center mx-auto text-rose-500">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="font-outfit font-extrabold text-base text-slate-800 dark:text-zinc-100">Hapus Task?</h3>
                <p id="delete-modal-text" class="text-xs text-slate-400 dark:text-zinc-500 leading-relaxed px-2"></p>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="button" id="confirm-delete-btn"
                        class="flex-1 px-4 py-2.5 text-white font-bold text-xs rounded-xl transition"
                        style="background:linear-gradient(to right,#f43f5e,#dc2626);box-shadow:0 4px 12px rgba(220,38,38,0.25);"
                        onmouseover="this.style.background='linear-gradient(to right,#e11d48,#b91c1c)'"
                        onmouseout="this.style.background='linear-gradient(to right,#f43f5e,#dc2626)'">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let activeFormId = null;

    function showConfirmDeleteModal(id, title) {
        activeFormId = 'delete-form-' + id;
        const modal = document.getElementById('delete-modal');
        const text = document.getElementById('delete-modal-text');
        text.innerHTML = `Apakah Anda yakin ingin menghapus task <span class="font-bold text-slate-700 dark:text-zinc-300">"${escapeHtml(title)}"</span> beserta semua sub-tugasnya? Tindakan ini tidak dapat dibatalkan.`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Trigger reflow for animation
        const content = modal.querySelector('div');
        requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
        });
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        const content = modal.querySelector('div');
        content.classList.add('scale-95', 'opacity-0');
        
        content.addEventListener('transitionend', function handler() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            activeFormId = null;
            content.removeEventListener('transitionend', handler);
        }, { once: true });
    }

    document.getElementById('confirm-delete-btn').addEventListener('click', () => {
        if (activeFormId) {
            document.getElementById(activeFormId).submit();
        }
    });

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function toggleSubtaskStatus(id, checkbox) {
        const isChecked = checkbox.checked;
        const url = `/tasks/details/${id}/toggle`;

        // Temporarily disable check during transit to avoid double clicks
        checkbox.disabled = true;

        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            checkbox.disabled = false;
            if (data.success) {
                const done = data.status === 'Completed';

                // 1. Update left-side checkmark/number
                const circle = document.getElementById(`circle-${id}`);
                if (circle) {
                    if (done) {
                        circle.className = "inline-flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-extrabold transition-all duration-200 bg-emerald-500 text-white";
                        circle.innerHTML = `
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        `;
                    } else {
                        circle.className = "inline-flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-extrabold transition-all duration-200 bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500";
                        circle.innerHTML = circle.getAttribute('data-index') || '1';
                    }
                }

                // 2. Update task detail name (line-through / normal)
                const name = document.getElementById(`name-${id}`);
                if (name) {
                    if (done) {
                        name.className = "font-semibold transition-all duration-200 line-through text-slate-400 dark:text-zinc-500";
                    } else {
                        name.className = "font-semibold transition-all duration-200 text-slate-800 dark:text-zinc-200";
                    }
                }


                // 4. Update parent task progress percentage and text
                const pct = data.progressPct;
                const progressCircle = document.getElementById('progress-circle');
                const progressText = document.getElementById('progress-text');
                const progressSubtext = document.getElementById('progress-subtext');

                if (progressCircle) {
                    progressCircle.setAttribute('stroke-dasharray', `${pct} ${100 - pct}`);
                }
                if (progressText) {
                    progressText.innerText = `${pct}%`;
                }
                if (progressSubtext) {
                    progressSubtext.innerText = `${data.completedCount}/${data.detailsCount} selesai`;
                }

                // 5. Update sub-task details table header count
                const tableCompleted = document.getElementById('table-completed-count');
                const tableDetailsCount = document.getElementById('table-details-count');
                if (tableCompleted) {
                    tableCompleted.innerText = data.completedCount;
                }
                if (tableDetailsCount) {
                    tableDetailsCount.innerText = data.detailsCount;
                }
            } else {
                checkbox.checked = !isChecked;
                alert('Gagal memperbarui status sub-task.');
            }
        })
        .catch(err => {
            checkbox.disabled = false;
            checkbox.checked = !isChecked;
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }
</script>
@endpush
