@extends('layouts.app')

@section('title', 'Notes - TaskManager')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">

    {{-- Keep-style Create Note Box --}}
    <div class="relative">
        <form id="create-note-form" class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 shadow-md transition-all duration-300 max-w-xl mx-auto overflow-hidden">
            @csrf
            <input type="hidden" name="color" id="new-note-color" value="#ffffff">
            <input type="hidden" name="is_pinned" id="new-note-pinned" value="0">

            {{-- Title Row (Hidden by default) --}}
            <div id="new-title-container" class="hidden px-4 pt-3 flex items-center justify-between">
                <input type="text" name="title" placeholder="Judul" 
                       class="w-full bg-transparent border-0 text-sm font-bold placeholder-slate-400 dark:placeholder-zinc-500 text-slate-800 dark:text-zinc-100 focus:ring-0 focus:outline-none p-0">
                <button type="button" onclick="toggleNewNotePin()" id="new-pin-btn" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Sematkan catatan">
                    <svg class="w-4.5 h-4.5 fill-none -rotate-45 transition-all duration-300" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="17" x2="12" y2="22"></line>
                        <path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.24V5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v4.24c0 .43-.14.85-.4 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24Z"></path>
                    </svg>
                </button>
            </div>

            {{-- Content Row --}}
            <div class="px-4 py-3 flex items-center gap-3">
                <textarea name="content" id="new-content-input" placeholder="Buat catatan..." rows="1"
                          onfocus="expandCreateBox()"
                          oninput="adjustTextareaHeight(this)"
                          onkeydown="handleTextareaKeyDown(e || event)"
                          class="w-full bg-transparent border-0 text-xs font-semibold placeholder-slate-400 dark:placeholder-zinc-500 text-slate-700 dark:text-zinc-200 focus:ring-0 focus:outline-none resize-none p-0 min-h-[24px]"
                          style="overflow-y: hidden; height: auto;"></textarea>
            </div>

            {{-- Toolbar Row (Hidden by default) --}}
            <div id="new-toolbar-container" class="hidden px-4 pb-3 flex items-center justify-between border-t border-slate-100/50 dark:border-zinc-800/50 pt-2">
                <div class="flex items-center gap-1.5">
                    {{-- Bold --}}
                    <button type="button" onclick="insertFormat('new-content-input', 'bold')" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Tebal">
                        <span class="font-bold text-xs px-0.5">B</span>
                    </button>
                    {{-- Italic --}}
                    <button type="button" onclick="insertFormat('new-content-input', 'italic')" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Miring">
                        <span class="italic font-serif text-xs px-0.5">I</span>
                    </button>
                    {{-- Bullet List --}}
                    <button type="button" onclick="insertFormat('new-content-input', 'bullet')" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Daftar bullet">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    {{-- Checklist --}}
                    <button type="button" onclick="insertFormat('new-content-input', 'checklist')" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Daftar centang">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </button>

                    <span class="w-px h-4 bg-slate-200 dark:bg-zinc-800 mx-1"></span>

                    {{-- Color Palette button --}}
                    <div class="relative group/palette">
                        <button type="button" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Ubah warna">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </button>
                        {{-- Dropdown Palette --}}
                        <div class="absolute bottom-full left-0 mb-1 hidden group-focus-within/palette:flex group-hover/palette:flex flex-wrap gap-1 bg-white dark:bg-zinc-800 p-1.5 rounded-xl border border-slate-200/80 dark:border-zinc-700 shadow-xl z-20 w-36">
                            @foreach([
                                '#ffffff' => 'Bawaan',
                                '#f28b82' => 'Merah',
                                '#fbbc04' => 'Jingga',
                                '#fff475' => 'Kuning',
                                '#ccff90' => 'Hijau',
                                '#a7ffeb' => 'Teal',
                                '#cbf0f8' => 'Biru Muda',
                                '#aecbfa' => 'Biru',
                                '#d7aefb' => 'Ungu',
                                '#fdcfe8' => 'Pink'
                            ] as $hex => $name)
                                <button type="button" onclick="setNewNoteColor('{{ $hex }}')" class="w-6 h-6 rounded-full border border-slate-200 dark:border-zinc-700/60 shadow-sm focus:outline-none transition hover:scale-110" style="background-color: {{ $hex }};" title="{{ $name }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="collapseCreateBox()" class="px-3 py-1.5 text-[10px] font-bold text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 text-[10px] font-extrabold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition shadow-md shadow-indigo-600/10">Simpan</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Main Notes Section --}}
    <div id="notes-container" class="space-y-10">

        {{-- Section Pinned --}}
        <div id="section-pinned" class="{{ $pinnedNotes->isEmpty() ? 'hidden' : '' }} space-y-3">
            <h5 class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest px-1">Disematkan</h5>
            <div id="grid-pinned" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($pinnedNotes as $note)
                    @include('notes.partials.card', ['note' => $note])
                @endforeach
            </div>
        </div>

        {{-- Section Others --}}
        <div id="section-others" class="space-y-3">
            <h5 id="label-others" class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest px-1 {{ $pinnedNotes->isEmpty() ? 'hidden' : '' }}">Lainnya</h5>
            <div id="grid-others" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($otherNotes as $note)
                    @include('notes.partials.card', ['note' => $note])
                @empty
                    <div id="empty-state" class="col-span-full py-16 text-center text-slate-400 dark:text-zinc-500 text-xs font-medium bg-white dark:bg-zinc-900 border border-dashed border-slate-200 dark:border-zinc-800 rounded-3xl space-y-3">
                        <div class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center mx-auto text-indigo-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700 dark:text-zinc-300">Belum ada catatan</p>
                            <p class="text-[10px] text-slate-400 dark:text-zinc-500 mt-0.5">Mulai ketik sesuatu di atas untuk menambahkan catatan.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>


{{-- Custom Delete Note Confirmation Modal --}}
<div id="delete-note-modal" class="fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 hidden">
    <div id="delete-note-modal-card" class="bg-white dark:bg-zinc-900 w-full max-w-sm rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-200">
        <div class="p-6 text-center space-y-4">
            <div class="h-12 w-12 bg-rose-50 dark:bg-rose-950/30 rounded-2xl flex items-center justify-center mx-auto text-rose-500">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="font-outfit font-extrabold text-base text-slate-800 dark:text-zinc-100">Hapus Catatan?</h3>
                <p id="delete-note-modal-text" class="text-xs text-slate-400 dark:text-zinc-500 leading-relaxed px-2">Apakah Anda yakin ingin menghapus catatan ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeDeleteNoteModal()"
                        class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="button" id="confirm-delete-note-btn"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-rose-600/10">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Note Modal --}}
<div id="edit-note-modal" class="fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 hidden">
    <div id="edit-modal-card" class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-2xl overflow-hidden transition-all duration-200">
        <form id="edit-note-form" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-note-id">
            <input type="hidden" name="color" id="edit-note-color">

            <div class="flex items-center justify-between">
                <input type="text" name="title" id="edit-title-input" placeholder="Judul" 
                       class="w-full bg-transparent border-0 text-base font-bold placeholder-slate-400 dark:placeholder-zinc-500 text-slate-800 dark:text-zinc-100 focus:ring-0 focus:outline-none p-0">
                <button type="button" onclick="toggleEditNotePin()" id="edit-pin-btn" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Sematkan catatan">
                    <svg class="w-4.5 h-4.5 fill-none -rotate-45 transition-all duration-300" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="17" x2="12" y2="22"></line>
                        <path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.24V5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v4.24c0 .43-.14.85-.4 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24Z"></path>
                    </svg>
                </button>
            </div>

            <textarea name="content" id="edit-content-input" placeholder="Catatan..." rows="4" required
                      oninput="adjustTextareaHeight(this)"
                      onkeydown="handleTextareaKeyDown(e || event)"
                      class="w-full bg-transparent border-0 text-xs font-semibold placeholder-slate-400 dark:placeholder-zinc-500 text-slate-700 dark:text-zinc-200 focus:ring-0 focus:outline-none resize-none p-0"
                      style="overflow-y: hidden; height: auto;"></textarea>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-zinc-800/80">
                <div class="flex items-center gap-1.5">
                    {{-- Bold --}}
                    <button type="button" onclick="insertFormat('edit-content-input', 'bold')" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Tebal">
                        <span class="font-bold text-xs px-0.5">B</span>
                    </button>
                    {{-- Italic --}}
                    <button type="button" onclick="insertFormat('edit-content-input', 'italic')" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Miring">
                        <span class="italic font-serif text-xs px-0.5">I</span>
                    </button>
                    {{-- Bullet List --}}
                    <button type="button" onclick="insertFormat('edit-content-input', 'bullet')" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Daftar bullet">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    {{-- Checklist --}}
                    <button type="button" onclick="insertFormat('edit-content-input', 'checklist')" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Daftar centang">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </button>

                    <span class="w-px h-4 bg-slate-200 dark:bg-zinc-800 mx-1"></span>

                    {{-- Color Palette button --}}
                    <div class="relative group/edit-palette">
                        <button type="button" class="format-btn text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors p-1" title="Ubah warna">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </button>
                        {{-- Dropdown Palette --}}
                        <div class="absolute bottom-full left-0 mb-1 hidden group-focus-within/edit-palette:flex group-hover/edit-palette:flex flex-wrap gap-1 bg-white dark:bg-zinc-800 p-1.5 rounded-xl border border-slate-200/80 dark:border-zinc-700 shadow-xl z-20 w-36">
                            @foreach([
                                '#ffffff' => 'Bawaan',
                                '#f28b82' => 'Merah',
                                '#fbbc04' => 'Jingga',
                                '#fff475' => 'Kuning',
                                '#ccff90' => 'Hijau',
                                '#a7ffeb' => 'Teal',
                                '#cbf0f8' => 'Biru Muda',
                                '#aecbfa' => 'Biru',
                                '#d7aefb' => 'Ungu',
                                '#fdcfe8' => 'Pink'
                            ] as $hex => $name)
                                <button type="button" onclick="setEditNoteColor('{{ $hex }}')" class="w-6 h-6 rounded-full border border-slate-200 dark:border-zinc-700/60 shadow-sm focus:outline-none transition hover:scale-110" style="background-color: {{ $hex }};" title="{{ $name }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-3.5 py-1.5 text-[10px] font-bold text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 text-[10px] font-extrabold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition shadow-md shadow-indigo-600/10">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
'use strict';

// ══════════════════════════════════════ CORE UTILITIES ══════════════════════════════════════

// Auto-grow textarea height
function adjustTextareaHeight(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}

// Format helper untuk Bold, Italic, Bullet, dan Checklist
function insertFormat(id, type) {
    const ta = document.getElementById(id);
    const s = ta.selectionStart;
    const end = ta.selectionEnd;
    const val = ta.value;
    
    if (type === 'bold') {
        const text = val.substring(s, end);
        ta.value = val.substring(0, s) + '**' + (text || 'teks') + '**' + val.substring(end);
        ta.selectionStart = s + 2;
        ta.selectionEnd = s + 2 + (text || 'teks').length;
    } else if (type === 'italic') {
        const text = val.substring(s, end);
        ta.value = val.substring(0, s) + '*' + (text || 'teks') + '*' + val.substring(end);
        ta.selectionStart = s + 1;
        ta.selectionEnd = s + 1 + (text || 'teks').length;
    } else if (type === 'bullet') {
        ta.value = val.substring(0, s) + '\n- ' + val.substring(end);
        ta.selectionStart = ta.selectionEnd = s + 3;
    } else if (type === 'checklist') {
        ta.value = val.substring(0, s) + '\n[ ] ' + val.substring(end);
        ta.selectionStart = ta.selectionEnd = s + 5;
    }
    ta.focus();
    adjustTextareaHeight(ta);
}

// Enter Key list formatting automation
function handleTextareaKeyDown(e) {
    if (e.key === 'Enter') {
        const textarea = e.target;
        const val = textarea.value;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        
        const textBeforeCursor = val.substring(0, start);
        const lines = textBeforeCursor.split('\n');
        const currentLine = lines[lines.length - 1];
        
        let match = currentLine.match(/^(\s*)(-\s|\[\s\]\s|\[x\]\s)/i);
        
        if (match) {
            e.preventDefault();
            const prefix = match[0];
            
            if (currentLine.trim() === match[2].trim()) {
                const lineStart = start - currentLine.length;
                textarea.value = val.substring(0, lineStart) + val.substring(end);
                textarea.selectionStart = textarea.selectionEnd = lineStart;
            } else {
                const newPrefix = prefix.replace(/\[x\]/i, '[ ]');
                textarea.value = val.substring(0, start) + '\n' + newPrefix + val.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + 1 + newPrefix.length;
            }
            adjustTextareaHeight(textarea);
        }
    }
}

// JavaScript helper untuk parse bold dan italic inline markdown
function parseMarkdownInline(text) {
    if (!text) return '';
    let parsed = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-extrabold">$1</strong>');
    parsed = parsed.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
    return parsed;
}

// JavaScript parser untuk HTML render di card
function parseContentToHtml(content, isLightWhite) {
    if (!content) return '';
    const lines = content.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").split('\n');
    const parsedLines = lines.map(line => {
        let match;
        if ((match = line.match(/^\[\s\]\s(.*)/))) {
            const txt = parseMarkdownInline(match[1]);
            return `<span class="flex items-center gap-1.5 py-0.5"><input type="checkbox" disabled class="rounded border-slate-300 dark:border-zinc-700 text-indigo-600 focus:ring-0 w-3.5 h-3.5 pointer-events-none"> <span class="${isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800'}">${txt}</span></span>`;
        } else if ((match = line.match(/^\[x\]\s(.*)/i))) {
            const txt = parseMarkdownInline(match[1]);
            return `<span class="flex items-center gap-1.5 py-0.5 line-through opacity-60"><input type="checkbox" checked disabled class="rounded border-slate-300 dark:border-zinc-700 text-indigo-600 focus:ring-0 w-3.5 h-3.5 pointer-events-none"> <span class="${isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800'}">${txt}</span></span>`;
        } else if ((match = line.match(/^-\s(.*)/))) {
            const txt = parseMarkdownInline(match[1]);
            return `<span class="flex items-start gap-1.5 py-0.5"><span class="${isLightWhite ? 'text-indigo-500' : 'text-indigo-900'} mt-1.5 shrink-0 w-1.5 h-1.5 rounded-full bg-current"></span> <span class="${isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800'}">${txt}</span></span>`;
        } else {
            const txt = parseMarkdownInline(line);
            return `<div>${txt === '' ? '&nbsp;' : txt}</div>`;
        }
    });
    return parsedLines.join('');
}

// Close create box on clicking outside
document.addEventListener('click', function(e) {
    const form = document.getElementById('create-note-form');
    if (!form.contains(e.target) && !document.getElementById('edit-note-modal').contains(e.target)) {
        const content = document.getElementById('new-content-input').value.trim();
        if (!content) {
            collapseCreateBox();
        }
    }
});

function expandCreateBox() {
    document.getElementById('new-title-container').classList.remove('hidden');
    document.getElementById('new-toolbar-container').classList.remove('hidden');
    const input = document.getElementById('new-content-input');
    input.rows = 2;
    adjustTextareaHeight(input);
}

function collapseCreateBox() {
    document.getElementById('new-title-container').classList.add('hidden');
    document.getElementById('new-toolbar-container').classList.add('hidden');
    const input = document.getElementById('new-content-input');
    input.rows = 1;
    input.style.height = 'auto';
    document.getElementById('create-note-form').reset();
    setNewNoteColor('#ffffff');
    setNewNotePinned(0);
}

function setNewNoteColor(hex) {
    const cleanHex = hex || '#ffffff';
    document.getElementById('new-note-color').value = cleanHex;
    const form = document.getElementById('create-note-form');
    form.style.backgroundColor = cleanHex;
    
    const formatButtons = form.querySelectorAll('.format-btn');
    const titleInput = form.querySelector('input[name="title"]');
    const contentInput = document.getElementById('new-content-input');
    const isWhite = (cleanHex === '#ffffff');

    if (isWhite) {
        form.className = "bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 shadow-md transition-all duration-300 max-w-xl mx-auto overflow-hidden";
        form.removeAttribute('style');
        formatButtons.forEach(btn => {
            btn.className = "format-btn text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200 transition-colors p-1";
        });
        if (titleInput) {
            titleInput.className = "w-full bg-transparent border-0 text-sm font-bold placeholder-slate-400 dark:placeholder-zinc-500 text-slate-800 dark:text-zinc-100 focus:ring-0 focus:outline-none p-0";
        }
        if (contentInput) {
            contentInput.className = "w-full bg-transparent border-0 text-xs font-semibold placeholder-slate-400 dark:placeholder-zinc-500 text-slate-700 dark:text-zinc-200 focus:ring-0 focus:outline-none resize-none p-0 min-h-[24px]";
        }
    } else {
        form.className = "rounded-2xl border border-transparent shadow-md transition-all duration-300 max-w-xl mx-auto overflow-hidden text-slate-900";
        formatButtons.forEach(btn => {
            btn.className = "format-btn text-slate-800/60 hover:text-slate-900 transition-colors p-1";
        });
        if (titleInput) {
            titleInput.className = "w-full bg-transparent border-0 text-sm font-bold focus:ring-0 focus:outline-none p-0 text-slate-900 placeholder-slate-600";
        }
        if (contentInput) {
            contentInput.className = "w-full bg-transparent border-0 text-xs font-semibold focus:ring-0 focus:outline-none resize-none p-0 min-h-[24px] text-slate-800 placeholder-slate-600";
        }
    }

    // Refresh pin icon color styling based on the new note color
    const pinnedVal = parseInt(document.getElementById('new-note-pinned').value) || 0;
    setNewNotePinned(pinnedVal);
}

function setNewNotePinned(val) {
    document.getElementById('new-note-pinned').value = val;
    const btn = document.getElementById('new-pin-btn');
    const colorInput = document.getElementById('new-note-color');
    const hex = colorInput ? colorInput.value : '#ffffff';
    const isWhite = (hex === '#ffffff');

    if (val === 1) {
        if (isWhite) {
            btn.className = "text-indigo-600 dark:text-indigo-400 transition-colors p-1";
        } else {
            btn.className = "text-indigo-900 transition-colors p-1";
        }
    } else {
        if (isWhite) {
            btn.className = "text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200 transition-colors p-1";
        } else {
            btn.className = "text-slate-800/60 hover:text-slate-900 transition-colors p-1";
        }
    }

    const svg = btn.querySelector('svg');
    if (svg) {
        if (val === 1) {
            svg.setAttribute('class', 'w-4.5 h-4.5 fill-current rotate-0 transition-all duration-300');
        } else {
            svg.setAttribute('class', 'w-4.5 h-4.5 fill-none -rotate-45 transition-all duration-300');
        }
    }
}

function toggleNewNotePin() {
    const input = document.getElementById('new-note-pinned');
    const newVal = input.value === '1' ? 0 : 1;
    setNewNotePinned(newVal);
}

function applyColorToCard(el, hex) {
    const isLightWhite = (hex === '#ffffff' || !hex);
    
    // Icon Contrast Colors
    const iconColorClass = isLightWhite 
        ? 'text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200' 
        : 'text-slate-800/60 hover:text-slate-900';
    
    const isPinned = el.getAttribute('data-pinned') === '1';
    const pinIconColorClass = isPinned 
        ? (isLightWhite ? 'text-indigo-600 dark:text-indigo-400' : 'text-indigo-900') 
        : (isLightWhite ? 'text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200' : 'text-slate-800/60 hover:text-slate-900');

    if (isLightWhite) {
        el.className = "note-card bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 relative group flex flex-col justify-between";
        el.removeAttribute('style');
    } else {
        el.className = "note-card rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 relative group flex flex-col justify-between border border-transparent text-slate-900";
        el.style.backgroundColor = hex;
    }

    // Apply color contrast to child elements
    const pinIcon = el.querySelector('.pin-icon');
    if (pinIcon) {
        pinIcon.className = `pin-icon ${pinIconColorClass} opacity-0 group-hover:opacity-100 transition-all p-1`;
        const svg = pinIcon.querySelector('svg');
        if (svg) {
            if (isPinned) {
                svg.setAttribute('class', 'w-4 h-4 fill-current rotate-0 transition-all duration-300');
            } else {
                svg.setAttribute('class', 'w-4 h-4 fill-none -rotate-45 transition-all duration-300');
            }
        }
    }

    const paletteBtn = el.querySelector('.card-palette-btn');
    if (paletteBtn) {
        paletteBtn.className = `card-palette-btn ${iconColorClass} transition-colors p-1`;
    }

    const deleteBtn = el.querySelector('.card-delete-btn');
    if (deleteBtn) {
        deleteBtn.className = `card-delete-btn ${iconColorClass} hover:text-rose-500 transition-colors p-1`;
    }

    // Adapt note lists text colors inside card content
    const listItems = el.querySelectorAll('.note-content span span');
    listItems.forEach(span => {
        if (isLightWhite) {
            span.className = "text-slate-600 dark:text-zinc-300";
        } else {
            span.className = "text-slate-800";
        }
    });

    const bulletPoints = el.querySelectorAll('.note-content span span.rounded-full');
    bulletPoints.forEach(bullet => {
        if (isLightWhite) {
            bullet.className = "text-indigo-500 mt-1.5 shrink-0 w-1.5 h-1.5 rounded-full bg-current";
        } else {
            bullet.className = "text-indigo-900 mt-1.5 shrink-0 w-1.5 h-1.5 rounded-full bg-current";
        }
    });
}

// ══════════════════════════════════════ AJAX ACTIONS ══════════════════════════════════════

document.getElementById('create-note-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const content = document.getElementById('new-content-input').value.trim();
    if (!content) return;

    const formData = new FormData(this);
    try {
        const response = await fetch("{{ route('notes.store') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            collapseCreateBox();
            insertNoteToGrid(data.note);
        }
    } catch (err) {
        console.error('Gagal membuat catatan:', err);
    }
});

function insertNoteToGrid(note) {
    const emptyState = document.getElementById('empty-state');
    if (emptyState) emptyState.remove();

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = getNoteCardHtml(note);
    const card = tempDiv.firstElementChild;
    
    applyColorToCard(card, note.color);

    if (note.is_pinned) {
        const grid = document.getElementById('grid-pinned');
        grid.prepend(card);
        document.getElementById('section-pinned').classList.remove('hidden');
        document.getElementById('label-others').classList.remove('hidden');
    } else {
        const grid = document.getElementById('grid-others');
        grid.prepend(card);
    }
}

function getNoteCardHtml(note) {
    const isLightWhite = (note.color === '#ffffff' || !note.color);
    
    // Icon Contrast Colors
    const iconColorClass = isLightWhite 
        ? 'text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200' 
        : 'text-slate-800/60 hover:text-slate-900';
    
    const pinIconColorClass = note.is_pinned 
        ? (isLightWhite ? 'text-indigo-600 dark:text-indigo-400' : 'text-indigo-900') 
        : (isLightWhite ? 'text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200' : 'text-slate-800/60 hover:text-slate-900');

    const parsedContent = parseContentToHtml(note.content, isLightWhite);
    const escapedContent = (note.content || '').replace(/"/g, '&quot;');

    return `
    <div id="note-${note.id}" data-id="${note.id}" data-color="${note.color || '#ffffff'}" data-pinned="${note.is_pinned ? '1' : '0'}" data-raw-content="${escapedContent}"
         class="note-card bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 relative group flex flex-col justify-between">
        
        <div>
            {{-- Header/Pin --}}
            <div class="flex items-start justify-between gap-2 mb-1.5">
                <h4 class="note-title font-bold text-xs text-slate-800 dark:text-zinc-100 leading-snug truncate cursor-pointer" onclick="openEditModal(${note.id})">
                    ${note.title || ''}
                </h4>
                <button onclick="togglePin(${note.id})" class="pin-icon ${pinIconColorClass} opacity-0 group-hover:opacity-100 transition-all p-1" title="Sematkan">
                    <svg class="w-4 h-4 ${note.is_pinned ? 'fill-current rotate-0' : 'fill-none -rotate-45'} transition-all duration-300" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="17" x2="12" y2="22"></line>
                        <path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.24V5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v4.24c0 .43-.14.85-.4 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24Z"></path>
                    </svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="note-content text-[11px] font-medium leading-relaxed cursor-pointer max-h-40 overflow-hidden" onclick="openEditModal(${note.id})">
                ${parsedContent}
            </div>
        </div>

        {{-- Card Action Toolbar --}}
        <div class="flex items-center justify-end gap-2 mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
            {{-- Color Palette dropdown button --}}
            <div class="relative group/card-palette">
                <button class="card-palette-btn ${iconColorClass} transition-colors p-1" title="Ubah warna">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </button>
                <div class="absolute bottom-full right-0 mb-1 hidden group-focus-within/card-palette:flex group-hover/card-palette:flex flex-wrap gap-1 bg-white dark:bg-zinc-800 p-1.5 rounded-xl border border-slate-200/80 dark:border-zinc-700 shadow-xl z-20 w-36">
                    <button type="button" onclick="changeColor(${note.id}, '#ffffff')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #ffffff;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#f28b82')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #f28b82;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#fbbc04')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #fbbc04;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#fff475')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #fff475;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#ccff90')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #ccff90;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#a7ffeb')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #a7ffeb;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#cbf0f8')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #cbf0f8;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#aecbfa')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #aecbfa;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#d7aefb')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #d7aefb;"></button>
                    <button type="button" onclick="changeColor(${note.id}, '#fdcfe8')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: #fdcfe8;"></button>
                </div>
            </div>
            {{-- Delete Button --}}
            <button onclick="deleteNote(${note.id})" class="card-delete-btn ${iconColorClass} hover:text-rose-500 transition-colors p-1" title="Hapus catatan">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

    </div>`;
}

// AJAX: Toggle Pinned status
async function togglePin(id) {
    try {
        const response = await fetch(`/notes/${id}/pin`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            moveCard(id, data.is_pinned);
        }
    } catch (err) {
        console.error('Gagal sematkan:', err);
    }
}

function moveCard(id, isPinned) {
    const card = document.getElementById(`note-${id}`);
    if (!card) return;

    // Toggle attribute
    card.setAttribute('data-pinned', isPinned ? '1' : '0');
    
    // Recalculate contrast and colors
    applyColorToCard(card, card.getAttribute('data-color'));

    // Remove from old grid
    card.remove();

    if (isPinned) {
        const grid = document.getElementById('grid-pinned');
        grid.prepend(card);
    } else {
        const grid = document.getElementById('grid-others');
        grid.prepend(card);
    }

    // Check Pinned Section visibility
    const gridPinned = document.getElementById('grid-pinned');
    const sectionPinned = document.getElementById('section-pinned');
    const labelOthers = document.getElementById('label-others');
    
    if (gridPinned.children.length > 0) {
        sectionPinned.classList.remove('hidden');
        labelOthers.classList.remove('hidden');
    } else {
        sectionPinned.classList.add('hidden');
        labelOthers.classList.add('hidden');
    }
}

// AJAX: Change color of card
async function changeColor(id, hex) {
    try {
        const response = await fetch(`/notes/${id}/color`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ color: hex })
        });
        const data = await response.json();
        if (data.success) {
            const card = document.getElementById(`note-${id}`);
            if (card) {
                card.setAttribute('data-color', hex);
                applyColorToCard(card, hex);
            }
        }
    } catch (err) {
        console.error('Gagal ganti warna:', err);
    }
}

// AJAX: Delete Note
let noteIdToDelete = null;

function deleteNote(id) {
    noteIdToDelete = id;
    const modal = document.getElementById('delete-note-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    const card = document.getElementById('delete-note-modal-card');
    requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
    });
}

function closeDeleteNoteModal() {
    const modal = document.getElementById('delete-note-modal');
    const card = document.getElementById('delete-note-modal-card');
    card.classList.add('scale-95', 'opacity-0');
    
    card.addEventListener('transitionend', function handler() {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        noteIdToDelete = null;
        card.removeEventListener('transitionend', handler);
    }, { once: true });
}

document.getElementById('confirm-delete-note-btn').addEventListener('click', async () => {
    if (!noteIdToDelete) return;
    const id = noteIdToDelete;
    closeDeleteNoteModal();

    try {
        const response = await fetch(`/notes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            const card = document.getElementById(`note-${id}`);
            if (card) {
                card.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    card.remove();
                    // Refcheck empty states
                    const gridPinned = document.getElementById('grid-pinned');
                    const gridOthers = document.getElementById('grid-others');
                    
                    if (gridPinned.children.length === 0) {
                        document.getElementById('section-pinned').classList.add('hidden');
                        document.getElementById('label-others').classList.add('hidden');
                    }
                    if (gridPinned.children.length === 0 && gridOthers.children.length === 0) {
                        // Re-add empty state
                        const container = document.getElementById('grid-others');
                        container.innerHTML = `
                        <div id="empty-state" class="col-span-full py-16 text-center text-slate-400 dark:text-zinc-500 text-xs font-medium bg-white dark:bg-zinc-900 border border-dashed border-slate-200 dark:border-zinc-800 rounded-3xl space-y-3">
                            <div class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center mx-auto text-indigo-500">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700 dark:text-zinc-300">Belum ada catatan</p>
                                <p class="text-[10px] text-slate-400 dark:text-zinc-500 mt-0.5">Mulai ketik sesuatu di atas untuk menambahkan catatan.</p>
                            </div>
                        </div>`;
                    }
                }, 200);
            }
        }
    } catch (err) {
        console.error('Gagal menghapus:', err);
    }
});

// ══════════════════════════════════════ EDIT MODAL ══════════════════════════════════════

let editNoteIsPinned = false;

function openEditModal(id) {
    const card = document.getElementById(`note-${id}`);
    if (!card) return;

    const title = card.querySelector('.note-title').textContent.trim();
    const content = card.getAttribute('data-raw-content') || '';
    const color = card.getAttribute('data-color');
    const isPinned = card.getAttribute('data-pinned') === '1';

    document.getElementById('edit-note-id').value = id;
    document.getElementById('edit-title-input').value = title;
    
    const contentTa = document.getElementById('edit-content-input');
    contentTa.value = content;
    
    setEditNoteColor(color || '#ffffff');
    setEditNotePinnedState(isPinned);

    const modal = document.getElementById('edit-note-modal');
    modal.classList.remove('hidden');
    
    // Auto adjust height on modal load
    setTimeout(() => {
        adjustTextareaHeight(contentTa);
    }, 50);
}

function closeEditModal() {
    document.getElementById('edit-note-modal').classList.add('hidden');
    document.getElementById('edit-note-form').reset();
}

function setEditNoteColor(hex) {
    const cleanHex = hex || '#ffffff';
    document.getElementById('edit-note-color').value = cleanHex;
    const card = document.getElementById('edit-modal-card');
    card.style.backgroundColor = cleanHex;
    
    const formatButtons = card.querySelectorAll('.format-btn');
    const titleInput = document.getElementById('edit-title-input');
    const contentInput = document.getElementById('edit-content-input');
    const isWhite = (cleanHex === '#ffffff');

    if (isWhite) {
        card.className = "bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-2xl overflow-hidden transition-all duration-200";
        card.removeAttribute('style');
        formatButtons.forEach(btn => {
            btn.className = "format-btn text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200 transition-colors p-1";
        });
        if (titleInput) {
            titleInput.className = "w-full bg-transparent border-0 text-base font-bold placeholder-slate-400 dark:placeholder-zinc-500 text-slate-800 dark:text-zinc-100 focus:ring-0 focus:outline-none p-0";
        }
        if (contentInput) {
            contentInput.className = "w-full bg-transparent border-0 text-xs font-semibold placeholder-slate-400 dark:placeholder-zinc-500 text-slate-700 dark:text-zinc-200 focus:ring-0 focus:outline-none resize-none p-0";
        }
    } else {
        card.className = "w-full max-w-md rounded-2xl border border-transparent shadow-2xl overflow-hidden transition-all duration-200 text-slate-900";
        formatButtons.forEach(btn => {
            btn.className = "format-btn text-slate-800/60 hover:text-slate-900 transition-colors p-1";
        });
        if (titleInput) {
            titleInput.className = "w-full bg-transparent border-0 text-base font-bold focus:ring-0 focus:outline-none p-0 text-slate-900 placeholder-slate-600";
        }
        if (contentInput) {
            contentInput.className = "w-full bg-transparent border-0 text-xs font-semibold focus:ring-0 focus:outline-none resize-none p-0 text-slate-800 placeholder-slate-600";
        }
    }

    // Refresh pin icon color styling based on the new note color
    setEditNotePinnedState(editNoteIsPinned);
}

function setEditNotePinnedState(isPinned) {
    editNoteIsPinned = isPinned;
    const btn = document.getElementById('edit-pin-btn');
    const colorInput = document.getElementById('edit-note-color');
    const hex = colorInput ? colorInput.value : '#ffffff';
    const isWhite = (hex === '#ffffff');

    if (isPinned) {
        if (isWhite) {
            btn.className = "text-indigo-600 dark:text-indigo-400 transition-colors p-1";
        } else {
            btn.className = "text-indigo-900 transition-colors p-1";
        }
    } else {
        if (isWhite) {
            btn.className = "text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200 transition-colors p-1";
        } else {
            btn.className = "text-slate-800/60 hover:text-slate-900 transition-colors p-1";
        }
    }

    const svg = btn.querySelector('svg');
    if (svg) {
        if (isPinned) {
            svg.setAttribute('class', 'w-4.5 h-4.5 fill-current rotate-0 transition-all duration-300');
        } else {
            svg.setAttribute('class', 'w-4.5 h-4.5 fill-none -rotate-45 transition-all duration-300');
        }
    }
}

function toggleEditNotePin() {
    setEditNotePinnedState(!editNoteIsPinned);
}

// Modal Submit: Update Note
document.getElementById('edit-note-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('edit-note-id').value;
    const title = document.getElementById('edit-title-input').value;
    const content = document.getElementById('edit-content-input').value;
    const color = document.getElementById('edit-note-color').value;

    try {
        const response = await fetch(`/notes/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: title,
                content: content,
                color: color,
                is_pinned: editNoteIsPinned
            })
        });
        const data = await response.json();
        if (data.success) {
            closeEditModal();
            
            // Update Card DOM
            const card = document.getElementById(`note-${id}`);
            if (card) {
                card.querySelector('.note-title').textContent = data.note.title || '';
                
                // Update raw content attribute
                card.setAttribute('data-raw-content', data.note.content);
                
                const isLightWhite = (data.note.color === '#ffffff' || !data.note.color);
                card.querySelector('.note-content').innerHTML = parseContentToHtml(data.note.content, isLightWhite);
                
                // Set color & pin layout
                applyColorToCard(card, data.note.color);
                moveCard(id, data.note.is_pinned);
            }
        }
    } catch (err) {
        console.error('Gagal update catatan:', err);
    }
});

// Close modal on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});
</script>
@endpush
