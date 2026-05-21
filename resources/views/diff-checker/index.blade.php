@extends('layouts.app')

@section('title', 'Diff Checker - TaskManager')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit font-black text-2xl lg:text-3xl bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300 bg-clip-text text-transparent">
                Diff Checker
            </h1>
            <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Bandingkan kode <em>before</em> dan <em>after</em> untuk melihat perubahan — GitHub Style.</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm p-6 space-y-6">

        <!-- Input Editors -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-zinc-300 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                        Original Code (Before)
                    </span>
                </label>
                <div style="display:flex;background:#0d1117;border-radius:12px;border:1px solid #30363d;overflow:hidden;font-family:'Fira Code','Cascadia Code',Consolas,monospace;">
                    <div id="gutter-before"
                         style="min-width:44px;padding:8px 8px 8px 0;background:#010409;text-align:right;color:#484f58;font-size:11px;line-height:1.7;user-select:none;overflow:hidden;white-space:pre;border-right:1px solid #21262d;">1
</div>
                    <textarea id="code-before" data-gid="gutter-before"
                              oninput="syncGutter(this,this.dataset.gid)"
                              onscroll="syncGutterScroll(this,this.dataset.gid)"
                              onkeydown="handleCodeTab(event,this.dataset.gid)"
                              placeholder="// Paste original code here..."
                              style="flex:1;min-height:240px;background:transparent;color:#c9d1d9;padding:8px;font-size:11px;line-height:1.7;border:none;outline:none;resize:vertical;tab-size:4;caret-color:#58a6ff;"></textarea>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-zinc-300 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        Changed Code (After)
                    </span>
                </label>
                <div style="display:flex;background:#0d1117;border-radius:12px;border:1px solid #30363d;overflow:hidden;font-family:'Fira Code','Cascadia Code',Consolas,monospace;">
                    <div id="gutter-after"
                         style="min-width:44px;padding:8px 8px 8px 0;background:#010409;text-align:right;color:#484f58;font-size:11px;line-height:1.7;user-select:none;overflow:hidden;white-space:pre;border-right:1px solid #21262d;">1
</div>
                    <textarea id="code-after" data-gid="gutter-after"
                              oninput="syncGutter(this,this.dataset.gid)"
                              onscroll="syncGutterScroll(this,this.dataset.gid)"
                              onkeydown="handleCodeTab(event,this.dataset.gid)"
                              placeholder="// Paste changed code here..."
                              style="flex:1;min-height:240px;background:transparent;color:#c9d1d9;padding:8px;font-size:11px;line-height:1.7;border:none;outline:none;resize:vertical;tab-size:4;caret-color:#58a6ff;"></textarea>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex flex-wrap items-center gap-4">
            <button onclick="compareCode()"
                    class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-bold text-sm rounded-xl transition shadow-md shadow-indigo-600/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                Bandingkan Kode
            </button>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-slate-600 dark:text-zinc-400">Tampilan:</span>
                <select id="diff-layout" onchange="compareCode()"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                    <option value="unified">Unified (Line-by-Line)</option>
                    <option value="split" selected>Split (Side-by-Side)</option>
                </select>
            </div>
            <div id="diff-stats" style="display:none;align-items:center;gap:10px;font-size:12px;font-weight:700;">
                <span id="stat-add" style="color:#3fb950;"></span>
                <span style="color:#484f58;">·</span>
                <span id="stat-del" style="color:#f85149;"></span>
            </div>
        </div>

        <!-- Diff Output -->
        <div id="diff-output" style="display:none;border-radius:12px;overflow:hidden;border:1px solid #30363d;">
            <div id="diff-table-wrap" style="overflow-x:auto;"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsdiff/5.1.0/diff.min.js"></script>
<script>
'use strict';

/* ═══════════════════════════════ IDE Editor Helpers ═══════════════════════════════ */
function syncGutter(ta, gid) {
    const g = document.getElementById(gid);
    if (!g) return;
    const n = (ta.value + '\n').split('\n').length;
    let o = '';
    for (let i = 1; i < n; i++) o += i + '\n';
    g.textContent = o;
    g.scrollTop = ta.scrollTop;
}
function syncGutterScroll(ta, gid) {
    const g = document.getElementById(gid);
    if (g) g.scrollTop = ta.scrollTop;
}
function handleCodeTab(e, gid) {
    if (e.key !== 'Tab') return;
    e.preventDefault();
    const ta = e.target, s = ta.selectionStart, end = ta.selectionEnd;
    ta.value = ta.value.substring(0, s) + '    ' + ta.value.substring(end);
    ta.selectionStart = ta.selectionEnd = s + 4;
    syncGutter(ta, gid);
}

/* ═══════════════════════════════ Theme Constants ═══════════════════════════════ */
const T = {
    bg:           '#0d1117',
    gutterBg:     '#010409',
    gutterBorder: '#21262d',
    gutterColor:  '#484f58',
    text:         '#c9d1d9',
    font:         "'Fira Code','Cascadia Code',Consolas,monospace",
    fontSize:     '11px',
    lineH:        '1.7',
    addBg:        'rgba(46,160,67,0.15)',
    addGutter:    'rgba(46,160,67,0.25)',
    addPrefix:    '#3fb950',
    delBg:        'rgba(248,81,73,0.15)',
    delGutter:    'rgba(248,81,73,0.25)',
    delPrefix:    '#f85149',
    emptyBg:      '#010409',
};

/* ═══════════════════════════════ Utilities ═══════════════════════════════ */
function esc(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function splitLines(value) {
    const lines = value.split('\n');
    if (lines[lines.length - 1] === '') lines.pop();
    return lines;
}

/* ═══════════════════════════════ Row Builders ═══════════════════════════════ */
const G = `padding:0 10px;text-align:right;color:${T.gutterColor};border-right:1px solid ${T.gutterBorder};user-select:none;min-width:40px;vertical-align:top;white-space:nowrap;font-family:${T.font};font-size:${T.fontSize};line-height:${T.lineH};`;
const C = (bg) => `padding:1px 12px;color:${T.text};background:${bg};white-space:pre-wrap;word-break:break-word;vertical-align:top;font-family:${T.font};font-size:${T.fontSize};line-height:${T.lineH};`;
const P = (bg, color) => `padding:0 8px;color:${color};font-weight:700;background:${bg};user-select:none;vertical-align:top;font-family:${T.font};font-size:${T.fontSize};line-height:${T.lineH};`;

function unifiedRow(lnO, lnN, type, text) {
    const rowBg  = type === 'add' ? T.addBg   : type === 'del' ? T.delBg   : T.bg;
    const gutBg  = type === 'add' ? T.addGutter : type === 'del' ? T.delGutter : T.gutterBg;
    const prefix = type === 'add' ? '+' : type === 'del' ? '-' : ' ';
    const pColor = type === 'add' ? T.addPrefix : type === 'del' ? T.delPrefix : T.gutterColor;
    return `<tr style="background:${rowBg};">
        <td style="${G}background:${gutBg};">${lnO != null ? lnO : ''}</td>
        <td style="${G}background:${gutBg};">${lnN != null ? lnN : ''}</td>
        <td style="${P(gutBg, pColor)}">${esc(prefix)}</td>
        <td style="${C(rowBg)}">${esc(text)}</td>
    </tr>`;
}

function splitRow(left, right) {
    const lBg   = left.type === 'del'   ? T.delBg    : left.type  === 'empty' ? T.emptyBg : T.bg;
    const rBg   = right.type === 'add'  ? T.addBg    : right.type === 'empty' ? T.emptyBg : T.bg;
    const lGut  = left.type === 'del'   ? T.delGutter  : T.gutterBg;
    const rGut  = right.type === 'add'  ? T.addGutter  : T.gutterBg;
    const lPfx  = left.type === 'del'   ? T.delPrefix  : T.gutterColor;
    const rPfx  = right.type === 'add'  ? T.addPrefix  : T.gutterColor;
    const lSym  = left.type  === 'del'  ? '-' : ' ';
    const rSym  = right.type === 'add'  ? '+' : ' ';
    const divider = `border-left:1px solid ${T.gutterBorder};`;
    return `<tr>
        <td style="${G}background:${lGut};">${left.ln != null ? left.ln : ''}</td>
        <td style="${P(lGut, lPfx)}">${left.type !== 'empty' ? esc(lSym) : ''}</td>
        <td style="${C(lBg)}">${esc(left.text)}</td>
        <td style="${G}background:${rGut};${divider}">${right.ln != null ? right.ln : ''}</td>
        <td style="${P(rGut, rPfx)}">${right.type !== 'empty' ? esc(rSym) : ''}</td>
        <td style="${C(rBg)}">${esc(right.text)}</td>
    </tr>`;
}

/* ═══════════════════════════════ Diff Builders ═══════════════════════════════ */
function buildUnified(diff) {
    let lnO = 1, lnN = 1, adds = 0, dels = 0, rows = '';
    diff.forEach(part => {
        const lines = splitLines(part.value);
        if (part.added) {
            adds += lines.length;
            lines.forEach(l => { rows += unifiedRow(null, lnN++, 'add', l); });
        } else if (part.removed) {
            dels += lines.length;
            lines.forEach(l => { rows += unifiedRow(lnO++, null, 'del', l); });
        } else {
            lines.forEach(l => { rows += unifiedRow(lnO++, lnN++, 'ctx', l); });
        }
    });
    return { rows, adds, dels };
}

function buildSplit(diff) {
    let lnO = 1, lnN = 1, adds = 0, dels = 0, pairs = [];

    for (let i = 0; i < diff.length; i++) {
        const part  = diff[i];
        const lines = splitLines(part.value);

        if (!part.added && !part.removed) {
            // Context — same on both sides
            lines.forEach(l => {
                pairs.push({
                    left:  { type: 'ctx', ln: lnO++, text: l },
                    right: { type: 'ctx', ln: lnN++, text: l },
                });
            });
        } else if (part.removed) {
            dels += lines.length;
            const next = diff[i + 1];
            if (next && next.added) {
                // Pair removed + added as replacement block
                i++;
                adds += next.value ? splitLines(next.value).length : 0;
                const addedLines = splitLines(next.value);
                const maxLen = Math.max(lines.length, addedLines.length);
                for (let j = 0; j < maxLen; j++) {
                    pairs.push({
                        left:  j < lines.length
                                ? { type: 'del', ln: lnO++, text: lines[j] }
                                : { type: 'empty', ln: null, text: '' },
                        right: j < addedLines.length
                                ? { type: 'add', ln: lnN++, text: addedLines[j] }
                                : { type: 'empty', ln: null, text: '' },
                    });
                }
            } else {
                lines.forEach(l => {
                    pairs.push({ left: { type: 'del', ln: lnO++, text: l }, right: { type: 'empty', ln: null, text: '' } });
                });
            }
        } else if (part.added) {
            adds += lines.length;
            lines.forEach(l => {
                pairs.push({ left: { type: 'empty', ln: null, text: '' }, right: { type: 'add', ln: lnN++, text: l } });
            });
        }
    }

    let rows = '';
    pairs.forEach(p => { rows += splitRow(p.left, p.right); });
    return { rows, adds, dels };
}

/* ═══════════════════════════════ Main Compare ═══════════════════════════════ */
function compareCode() {
    const before   = document.getElementById('code-before').value;
    const after    = document.getElementById('code-after').value;
    const layout   = document.getElementById('diff-layout').value;
    const output   = document.getElementById('diff-output');
    const wrap     = document.getElementById('diff-table-wrap');
    const stats    = document.getElementById('diff-stats');

    if (!before && !after) { output.style.display = 'none'; return; }

    const diff = Diff.diffLines(before, after);

    const tableBase = `font-family:${T.font};font-size:${T.fontSize};line-height:${T.lineH};border-collapse:collapse;width:100%;background:${T.bg};`;

    let result;
    if (layout === 'unified') {
        result = buildUnified(diff);
        wrap.innerHTML = `<table style="${tableBase}">${result.rows}</table>`;
    } else {
        result = buildSplit(diff);
        wrap.innerHTML = `<table style="${tableBase}"><colgroup><col><col><col style="width:45%"><col><col><col style="width:45%"></colgroup>${result.rows}</table>`;
    }

    // Stats bar
    document.getElementById('stat-add').textContent = `+${result.adds} additions`;
    document.getElementById('stat-del').textContent = `-${result.dels} deletions`;
    stats.style.display = 'flex';

    output.style.display = 'block';
}
</script>
@endpush
