@extends('layouts.app')

@section('title', 'Tambah Task Baru | TaskManager')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-zinc-500">
        <a href="{{ route('tasks.index') }}" class="hover:text-indigo-500 transition font-semibold">Task Manager</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-600 dark:text-zinc-300 font-semibold">Tambah Task Baru</span>
    </div>

    <form action="{{ route('tasks.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Task Header Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-800/40 bg-slate-50/60 dark:bg-zinc-900/30">
                <h2 class="font-outfit font-extrabold text-base">Informasi Utama Task</h2>
                <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">Isi data header task yang menjadi induk dari sub-tugas detail.</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Title --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">Judul <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none placeholder-slate-400 dark:placeholder-zinc-500 @error('title') ring-2 ring-rose-500 @enderror">
                    @error('title')<p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
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
                            <option value="Adit" @selected(old('user') === 'Adit')>Adit</option>
                            <option value="User 1" @selected(old('user') === 'User 1')>User 1</option>
                            <!-- <option value="Idhamsyah" @selected(old('user') === 'Idhamsyah')>Idhamsyah</option> -->
                        </select>
                    </div>
                    @error('user')<p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Path (plain text) --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">
                        Path file
                        <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="path" value="{{ old('path') }}"
       class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none placeholder-slate-400 dark:placeholder-zinc-500 @error('path') ring-2 ring-rose-500 @enderror">
                    @error('path')<p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Note --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-zinc-400 mb-1.5">Catatan</label>
                    <textarea name="note" rows="3" placeholder="Tambahkan catatan atau keterangan tambahan untuk task ini..."
                              class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none placeholder-slate-400 dark:placeholder-zinc-500 resize-none">{{ old('note') }}</textarea>
                    @error('note')<p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Task Details Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-800/40 bg-slate-50/60 dark:bg-zinc-900/30 flex items-center justify-between">
                <div>
                    <h2 class="font-outfit font-extrabold text-base">File</h2>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">Tambahkan daftar pekerjaan rinci yang termasuk dalam task ini.</p>
                </div>
                <button type="button" onclick="addDetailRow()"
                        class="inline-flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold text-xs px-3 py-1.5 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-950/60 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Baris
                </button>
            </div>
            <div class="p-6">
                <div class="hidden md:grid grid-cols-12 gap-3 mb-2 px-1 text-[10px] font-extrabold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">
                    <div class="col-span-8">Nama File</div>
                    <div class="col-span-3">Status</div>
                    <div class="col-span-1"></div>
                </div>
                <div id="detail-rows" class="space-y-3"></div>
                <p id="empty-detail-note" class="text-center text-xs text-slate-400 dark:text-zinc-500 py-6 hidden">
                    Klik "Tambah Baris" untuk menambah File.
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between gap-4 pt-2">
            <a href="{{ route('tasks.index') }}"
               class="px-5 py-2.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 font-bold text-xs rounded-xl transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-600/20">
                Simpan Task
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let detailIndex = 0;

    function addDetailRow(data = {}) {
        const container = document.getElementById('detail-rows');
        const emptyNote  = document.getElementById('empty-detail-note');
        emptyNote.classList.add('hidden');

        const idx    = detailIndex++;
        const name   = data.name   ?? '';
        const desc   = data.desc   ?? '';
        const status = data.status ?? 'Pending';
        const idVal  = data.id     ?? '';

        const row = document.createElement('div');
        row.className = 'detail-row bg-slate-50 dark:bg-zinc-800/40 p-3 rounded-2xl space-y-2';
        const isCompleted = status === 'Completed';
        row.innerHTML = `
            ${idVal ? `<input type="hidden" name="details[${idx}][id]" value="${idVal}">` : ''}
            <div class="grid grid-cols-12 gap-3 items-center">
                <div class="col-span-12 md:col-span-8">
                    <input type="text" name="details[${idx}][name]" value="${escapeAttr(name)}" required
                           placeholder="Nama File..."
                           class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 placeholder-slate-400 dark:placeholder-zinc-500">
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
                    <button type="button" onclick="this.closest('.detail-row').remove(); checkEmpty()"
                            class="p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 text-slate-300 hover:text-rose-500 dark:text-zinc-600 dark:hover:text-rose-400 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div style="display:flex;background:#0d1117;border-radius:12px;border:1px solid #30363d;overflow:hidden;font-family:'Fira Code','Cascadia Code','Consolas',monospace;">
                <div id="gutter-${idx}"
                     style="min-width:44px;padding:8px 8px 8px 0;background:#010409;text-align:right;color:#484f58;font-size:11px;line-height:1.7;user-select:none;overflow:hidden;white-space:pre;border-right:1px solid #21262d;">1
</div>
                <textarea name="details[${idx}][desc]"
                          data-gid="gutter-${idx}"
                          oninput="syncGutter(this,this.dataset.gid)"
                          onscroll="syncGutterScroll(this,this.dataset.gid)"
                          onkeydown="handleCodeTab(event,this.dataset.gid)"
                          style="flex:1;min-height:120px;background:transparent;color:#c9d1d9;padding:8px;font-size:11px;line-height:1.7;border:none;outline:none;resize:vertical;tab-size:4;caret-color:#58a6ff;">${escapeHtml(desc)}</textarea>
            </div>
        `;
        container.appendChild(row);
        // Init line numbers for pre-filled content
        const gta = row.querySelector('[data-gid]');
        if (gta) syncGutter(gta, gta.dataset.gid);
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

    document.addEventListener('DOMContentLoaded', () => addDetailRow());
</script>
@endpush
