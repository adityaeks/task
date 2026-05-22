@extends('layouts.app')

@section('title', 'Edit Task | TaskManager')

@section('content')
<style>
    /* Prevent squishing and enable single row horizontal layout in Board view */
    #detail-board .detail-row {
        width: 300px !important;
        flex-shrink: 0 !important;
    }
    @media (min-width: 640px) {
        #detail-board .detail-row {
            width: 380px !important;
        }
    }
    #detail-board .detail-row .grid > div:nth-child(1) {
        grid-column: span 12 / span 12 !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.375rem !important;
    }
    #detail-board .detail-row .grid > div:nth-child(1) label {
        width: auto !important;
        flex-shrink: 0 !important;
    }
    #detail-board .detail-row .grid > div:nth-child(2) {
        grid-column: span 9 / span 9 !important;
    }
    #detail-board .detail-row .grid > div:nth-child(3) {
        grid-column: span 3 / span 3 !important;
    }

    /* Code Editor Container style */
    .code-editor-container {
        display: flex;
        background: #f8fafc; /* light mode: bg-slate-50 equivalent */
        border-radius: 12px;
        border: 1px solid #e2e8f0; /* light mode: border-slate-200 equivalent */
        overflow: hidden;
        font-family: 'Fira Code', 'Cascadia Code', 'Consolas', monospace;
        transition: all 0.2s ease-in-out;
    }
    .dark .code-editor-container {
        background: #0d1117; /* dark mode: GitHub dark bg */
        border: 1px solid #30363d;
    }

    /* Gutter style */
    .code-editor-gutter {
        min-width: 44px;
        padding: 8px 8px 8px 0;
        background: #f1f5f9; /* light mode: bg-slate-100 equivalent */
        text-align: right;
        color: #94a3b8; /* light mode: text-slate-400 */
        font-size: 11px;
        line-height: 1.7;
        user-select: none;
        overflow: hidden;
        white-space: pre;
        border-right: 1px solid #e2e8f0;
        transition: all 0.2s ease-in-out;
    }
    .dark .code-editor-gutter {
        background: #010409;
        color: #484f58;
        border-right: 1px solid #21262d;
    }

    /* Textarea style */
    .code-editor-textarea {
        flex: 1;
        min-height: 120px;
        background: transparent;
        color: #0f172a; /* light mode: text-slate-900 */
        padding: 8px;
        font-size: 11px;
        line-height: 1.7;
        border: none;
        outline: none;
        resize: vertical;
        tab-size: 4;
        caret-color: #4f46e5; /* light mode: indigo-600 */
        transition: all 0.2s ease-in-out;
    }
    .dark .code-editor-textarea {
        color: #c9d1d9;
        caret-color: #58a6ff;
    }
</style>
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-zinc-500">
        <a href="{{ route('tasks.index') }}" class="hover:text-indigo-500 transition font-semibold">Task Manager</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('tasks.show', $task) }}" class="hover:text-indigo-500 transition font-semibold truncate max-w-[200px]">{{ $task->title }}</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-600 dark:text-zinc-300 font-semibold">Edit</span>
    </div>

    <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Task Header Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-800/40 bg-slate-50/60 dark:bg-zinc-900/30">
                <h2 class="font-outfit font-extrabold text-base">Informasi Utama Task</h2>
                <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">Perbarui data header task beserta informasi penting terkait.</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Title --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">Judul Task <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $task->title) }}" required
                           class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none @error('title') ring-2 ring-rose-500 @enderror">
                    @error('title')<p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">Tanggal Tugas <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $task->date->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none @error('date') ring-2 ring-rose-500 @enderror">
                    @error('date')<p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- User --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">Assigned User</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <select name="user"
                                class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none @error('user') ring-2 ring-rose-500 @enderror">
                            <option value="">— Pilih User —</option>
                            <option value="Adit" @selected(old('user', $task->user) === 'Adit')>Adit</option>
                            <option value="User 1" @selected(old('user', $task->user) === 'User 1')>User 1</option>
                            <!-- <option value="Idhamsyah" @selected(old('user', $task->user) === 'Idhamsyah')>Idhamsyah</option> -->
                        </select>
                    </div>
                    @error('user')<p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Path (plain text) --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">
                        File Pendukung / Path
                        <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </span>
                        <input type="text" name="path" value="{{ old('path', $task->path) }}"
                               placeholder="e.g. https://drive.google.com/file/... atau /storage/docs/laporan.pdf"
                               class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none placeholder-slate-400 dark:placeholder-zinc-500 @error('path') ring-2 ring-rose-500 @enderror">
                    </div>
                    @error('path')<p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Note --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">Catatan</label>
                    <textarea name="note" rows="3"
                              class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none resize-none">{{ old('note', $task->note) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Task Details Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-800/40 bg-slate-50/60 dark:bg-zinc-900/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-outfit font-extrabold text-base">File</h2>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">Perbarui, tambah, atau hapus sub-tugas detail task ini.</p>
                </div>
                <div class="flex items-center gap-3 self-end sm:self-auto">
                    {{-- Segmented View Switcher --}}
                    <div class="flex items-center gap-1 bg-slate-100 dark:bg-zinc-800/60 p-1 rounded-xl">
                        <button type="button" onclick="setViewMode('row')" id="btn-view-row"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-300">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            List
                        </button>
                        <button type="button" onclick="setViewMode('board')" id="btn-view-board"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-indigo-600 dark:text-indigo-400 bg-white dark:bg-zinc-900 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                            Board
                        </button>
                    </div>

                    <button type="button" onclick="addDetailRow({}, true)"
                            class="inline-flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold text-xs px-3 py-1.5 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-950/60 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Baris
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="hidden grid-cols-12 gap-3 mb-2 px-1 text-[10px] font-extrabold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">
                    <div class="col-span-8">Nama File</div>
                    <div class="col-span-3">Status</div>
                    <div class="col-span-1"></div>
                </div>
                
                {{-- List/Row Container --}}
                <div id="detail-rows" class="space-y-3 hidden"></div>

                {{-- Board Layout Container --}}
                <div id="detail-board" class="flex flex-nowrap overflow-x-auto w-full gap-4 pb-3 scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-zinc-800"></div>

                <p id="empty-detail-note" class="text-center text-xs text-slate-400 dark:text-zinc-500 py-6 hidden">
                    Klik "Tambah Baris" untuk menambah File.
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between gap-4 pt-2">
            <a href="{{ route('tasks.show', $task) }}"
               class="px-5 py-2.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 font-bold text-xs rounded-xl transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-600/20">
                Perbarui Task
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let detailIndex = 0;
    let currentViewMode = 'board';
    const existingDetails = @json($task->details);
    const oldDetails = @json(old('details'));

    function showToast(message, type = 'success') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'flex items-center gap-3 px-4 py-3 bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 shadow-lg shadow-indigo-500/5 rounded-2xl text-xs font-bold text-slate-800 dark:text-zinc-200 pointer-events-auto transform translate-y-2 opacity-0 transition-all duration-300 ease-out';
        
        let iconHtml = '';
        if (type === 'success') {
            iconHtml = `
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </span>
            `;
        } else if (type === 'info') {
            iconHtml = `
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-500">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </span>
            `;
        }
        
        toast.innerHTML = `
            ${iconHtml}
            <span>${message}</span>
        `;

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        });

        // Animate out and remove
        setTimeout(() => {
            toast.classList.add('translate-y-1', 'opacity-0');
            toast.addEventListener('transitionend', () => {
                toast.remove();
                if (container.children.length === 0) {
                    container.remove();
                }
            });
        }, 3000);
    }

    function addDetailRow(data = {}, isManual = false) {
        const container = document.getElementById('detail-rows');
        const emptyNote  = document.getElementById('empty-detail-note');
        emptyNote.classList.add('hidden');

        const idx    = detailIndex++;
        let category = data.category ?? '';
        let name     = data.name     ?? '';
        const desc   = data.desc   ?? '';
        const status = data.status ?? 'Pending';
        const idVal  = data.id     ?? '';

        // Auto-detect category from database format (e.g. "controllers.File pertama")
        if (name && name.includes('.') && !category) {
            const parts = name.split('.');
            const firstPart = parts[0].toLowerCase();
            if (['controllers', 'model', 'view', 'js'].includes(firstPart)) {
                category = parts[0].charAt(0).toUpperCase() + parts[0].slice(1);
                name = parts.slice(1).join('.');
            }
        }

        let labelHtml = '';
        let inputClass = 'w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 placeholder-slate-400 dark:placeholder-zinc-500';
        let wrapperClass = 'col-span-12 md:col-span-8';

        if (category) {
            labelHtml = `<label class="text-xs font-bold text-slate-500 dark:text-zinc-400 shrink-0 w-24">${escapeHtml(category)}</label>`;
            inputClass = 'flex-1 px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 placeholder-slate-400 dark:placeholder-zinc-500';
            wrapperClass = 'col-span-12 md:col-span-8 flex items-center gap-3';
        }

        const row = document.createElement('div');
        row.className = 'detail-row bg-slate-50 dark:bg-zinc-800/40 p-3 rounded-2xl space-y-2';
        const isCompleted = status === 'Completed';
        row.innerHTML = `
            ${idVal ? `<input type="hidden" name="details[${idx}][id]" value="${idVal}">` : ''}
            <input type="hidden" name="details[${idx}][category]" value="${escapeAttr(category)}">
            <div class="grid grid-cols-12 gap-3 items-center">
                <div class="${wrapperClass}">
                    ${labelHtml}
                    <input type="text" name="details[${idx}][name]" value="${escapeAttr(name)}"
                           placeholder="Nama sub-tugas..."
                           class="${inputClass}">
                </div>
                <div class="col-span-10 md:col-span-3 flex items-center gap-2">
                    <input type="hidden" name="details[${idx}][status]" id="status-inp-${idx}" value="${status}">
                    <button type="button"
                            onclick="toggleDetailStatus(this, 'status-inp-${idx}', 'status-lbl-${idx}')"
                            data-active="${isCompleted}"
                            style="position:relative;width:36px;height:20px;flex-shrink:0;border:none;border-radius:9999px;cursor:pointer;transition:background-color .2s;background-color:${isCompleted ? '#10b981' : '#cbd5e1'}">
                        <span style="position:absolute;top:2px;left:2px;display:block;width:16px;height:16px;background:white;border-radius:9999px;box-shadow:0 1px 3px rgba(0,0,0,.25);transition:transform .2s;transform:${isCompleted ? 'translateX(16px)' : 'translateX(0)'}"></span>
                    </button>
                    <span id="status-lbl-${idx}" style="font-size:11px;font-weight:700;color:${isCompleted ? '#10b981' : '#94a3b8'}">${isCompleted ? 'Completed' : 'Pending'}</span>
                </div>
                <div class="col-span-2 md:col-span-1 flex items-center justify-end">
                    <button type="button" onclick="this.closest('.detail-row').remove(); checkEmpty(); showToast('Baris file dihapus!', 'info')"
                            class="p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 text-slate-300 hover:text-rose-500 dark:text-zinc-600 dark:hover:text-rose-400 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="code-editor-container">
                <div id="gutter-${idx}" class="code-editor-gutter">1</div>
                <textarea name="details[${idx}][desc]"
                          data-gid="gutter-${idx}"
                          oninput="syncGutter(this,this.dataset.gid)"
                          onscroll="syncGutterScroll(this,this.dataset.gid)"
                          onkeydown="handleCodeTab(event,this.dataset.gid)"
                          class="code-editor-textarea">${escapeHtml(desc)}</textarea>
            </div>
        `;

        if (currentViewMode === 'board') {
            document.getElementById('detail-board').appendChild(row);
        } else {
            container.appendChild(row);
        }

        // Init line numbers for pre-filled content
        const gta = row.querySelector('[data-gid]');
        if (gta) syncGutter(gta, gta.dataset.gid);

        if (isManual) {
            showToast('Baris file baru berhasil ditambahkan!');
        }
    }

    function toggleDetailStatus(btn, inputId, labelId) {
        const isNowOn = btn.getAttribute('data-active') !== 'true';
        btn.setAttribute('data-active', isNowOn);
        btn.style.backgroundColor = isNowOn ? '#10b981' : '#cbd5e1';
        btn.querySelector('span').style.transform = isNowOn ? 'translateX(16px)' : 'translateX(0)';
        document.getElementById(inputId).value = isNowOn ? 'Completed' : 'Pending';
        const lbl = document.getElementById(labelId);
        lbl.textContent = isNowOn ? 'Completed' : 'Pending';
        lbl.style.color = isNowOn ? '#10b981' : '#94a3b8';
    }

    function setViewMode(mode) {
        currentViewMode = mode;
        
        const btnRow = document.getElementById('btn-view-row');
        const btnBoard = document.getElementById('btn-view-board');
        const headerRow = document.querySelector('.hidden.md\\:grid.grid-cols-12.gap-3.mb-2');
        const detailRowsContainer = document.getElementById('detail-rows');
        const detailBoardContainer = document.getElementById('detail-board');
        
        if (mode === 'row') {
            btnRow.className = "inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-indigo-600 dark:text-indigo-400 bg-white dark:bg-zinc-900 shadow-sm";
            btnBoard.className = "inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-300";
            
            if (headerRow) {
                headerRow.classList.remove('md:hidden');
                headerRow.classList.add('md:grid');
            }
            detailRowsContainer.classList.remove('hidden');
            detailBoardContainer.classList.add('hidden');
            
            // Move all rows back to list sorted by index
            const allRows = Array.from(document.querySelectorAll('.detail-row'));
            allRows.sort((a, b) => {
                const idxA = parseInt(a.querySelector('input[name*="[status]"]').id.split('-').pop());
                const idxB = parseInt(b.querySelector('input[name*="[status]"]').id.split('-').pop());
                return idxA - idxB;
            });
            allRows.forEach(row => detailRowsContainer.appendChild(row));
        } else {
            btnBoard.className = "inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-indigo-600 dark:text-indigo-400 bg-white dark:bg-zinc-900 shadow-sm";
            btnRow.className = "inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-300";
            
            if (headerRow) {
                headerRow.classList.remove('md:grid');
                headerRow.classList.add('md:hidden');
            }
            detailRowsContainer.classList.add('hidden');
            detailBoardContainer.classList.remove('hidden');
            
            // Move all rows to board sorted by index
            const allRows = Array.from(document.querySelectorAll('.detail-row'));
            allRows.sort((a, b) => {
                const idxA = parseInt(a.querySelector('input[name*="[status]"]').id.split('-').pop());
                const idxB = parseInt(b.querySelector('input[name*="[status]"]').id.split('-').pop());
                return idxA - idxB;
            });
            allRows.forEach(row => detailBoardContainer.appendChild(row));
        }
        checkEmpty();
    }

    /* ── IDE-style code editor helpers ── */
    function syncGutter(ta, gutterId) {
        const g = document.getElementById(gutterId);
        if (!g) return;
        const lines = (ta.value + '\n').split('\n').length;
        let out = '';
        for (let i = 1; i < lines; i++) out += i + '\n';
        g.textContent = out;
        g.scrollTop = ta.scrollTop;
    }

    function syncGutterScroll(ta, gutterId) {
        const g = document.getElementById(gutterId);
        if (g) g.scrollTop = ta.scrollTop;
    }

    function handleCodeTab(e, gutterId) {
        if (e.key !== 'Tab') return;
        e.preventDefault();
        const ta = e.target;
        const s = ta.selectionStart, end = ta.selectionEnd;
        ta.value = ta.value.substring(0, s) + '    ' + ta.value.substring(end);
        ta.selectionStart = ta.selectionEnd = s + 4;
        syncGutter(ta, gutterId);
    }

    function checkEmpty() {
        const rows = document.querySelectorAll('.detail-row');
        document.getElementById('empty-detail-note').classList.toggle('hidden', rows.length > 0);
    }

    function escapeAttr(str) {
        return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (oldDetails && Object.keys(oldDetails).length > 0) {
            const detailsArray = Array.isArray(oldDetails) ? oldDetails : Object.values(oldDetails);
            detailsArray.forEach(d => addDetailRow(d));
        } else if (existingDetails.length > 0) {
            existingDetails.forEach(d => addDetailRow(d));
        } else {
            addDetailRow();
        }
    });
</script>
@endpush
