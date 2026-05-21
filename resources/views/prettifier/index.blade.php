@extends('layouts.app')

@section('title', 'Code Prettifier - TaskManager')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="font-outfit font-black text-2xl lg:text-3xl bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300 bg-clip-text text-transparent">
            Code Prettifier
        </h1>
        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Format dan rapikan kode secara otomatis. Pilih bahasa, tempel kode, lalu klik <strong>Format</strong>.</p>
    </div>

    {{-- Main Card --}}
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm p-6 space-y-5">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3">

            {{-- Language Selector --}}
            <div class="relative">
                <div id="lang-badge" class="absolute left-3 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full" style="background:#f6c90e;"></div>
                <select id="lang-select" onchange="onLangChange()"
                        class="pl-8 pr-10 py-2 text-xs font-bold rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none cursor-pointer appearance-none">
                    <option value="json"       data-color="#4ec9b0">JSON</option>
                    <option value="sql"        data-color="#569cd6">SQL</option>
                    <option value="javascript" data-color="#f6c90e" selected>JavaScript</option>
                    <option value="typescript" data-color="#3178c6">TypeScript</option>
                    <option value="html"       data-color="#e34c26">HTML</option>
                    <option value="css"        data-color="#264de4">CSS / SCSS</option>
                    <option value="xml"        data-color="#f1a65a">XML</option>
                    <option value="php"        data-color="#8892be">PHP</option>
                </select>
                <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            {{-- Tab Width --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">Tab:</span>
                <select id="tab-width" class="px-3 py-2 text-xs font-bold rounded-xl bg-slate-100 dark:bg-zinc-800 border-0 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                    <option value="2" selected>2 Spasi</option>
                    <option value="4">4 Spasi</option>
                </select>
            </div>

            <div class="flex-1"></div>

            {{-- Actions --}}
            <button onclick="clearAll()"
                    class="px-4 py-2 text-xs font-bold rounded-xl bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700 transition">
                Clear
            </button>
            <button onclick="copyInput()"
                    class="px-4 py-2 text-xs font-bold rounded-xl bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 hover:bg-slate-200 dark:hover:bg-zinc-700 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy Input
            </button>
            <button onclick="runFormat()"
                    id="btn-format"
                    class="px-6 py-2 text-xs font-bold rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white transition shadow-md shadow-indigo-600/20 flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span id="btn-label">Format</span>
            </button>
        </div>

        {{-- Language label badge --}}
        <div id="lang-info" class="flex items-center gap-2 text-[11px] font-semibold" style="color:#484f58;">
            <span id="lang-dot" class="w-2 h-2 rounded-full inline-block" style="background:#f6c90e;"></span>
            <span id="lang-name-label">JavaScript (Prettier)</span>
            <span id="lib-loading" class="hidden text-amber-500">⏳ Loading formatter...</span>
        </div>

        {{-- Error Banner --}}
        <div id="error-banner" class="hidden text-xs font-semibold px-4 py-3 rounded-xl flex items-start gap-2"
             style="background:rgba(248,81,73,0.12);color:#f85149;border:1px solid rgba(248,81,73,0.25);">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span id="error-text"></span>
        </div>

        {{-- Input Editor --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Input</span>
                <span id="input-lines" class="text-[10px] text-slate-400 dark:text-zinc-600 font-semibold">0 baris</span>
            </div>
            <div style="display:flex;background:#0d1117;border-radius:12px;border:1px solid #30363d;overflow:hidden;font-family:'Fira Code','Cascadia Code',Consolas,monospace;">
                <div id="gutter-in"
                     style="min-width:44px;padding:8px 8px 8px 0;background:#010409;text-align:right;color:#484f58;font-size:11px;line-height:1.7;user-select:none;overflow:hidden;white-space:pre;border-right:1px solid #21262d;">1
</div>
                <textarea id="code-in"
                          data-gid="gutter-in"
                          oninput="syncGutter(this,this.dataset.gid);updateLineCount()"
                          onscroll="syncGutterScroll(this,this.dataset.gid)"
                          onkeydown="handleCodeTab(event,this.dataset.gid)"
                          placeholder="// Paste your code here..."
                          style="flex:1;min-height:280px;background:transparent;color:#c9d1d9;padding:8px;font-size:11px;line-height:1.7;border:none;outline:none;resize:vertical;tab-size:4;caret-color:#58a6ff;"></textarea>
            </div>
        </div>

        {{-- Divider --}}
        <div class="flex items-center gap-4">
            <div class="flex-1 h-px bg-slate-200 dark:bg-zinc-800"></div>
            <div id="format-arrow"
                 class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center shadow-md shadow-indigo-500/30 text-white">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>
            <div class="flex-1 h-px bg-slate-200 dark:bg-zinc-800"></div>
        </div>

        {{-- Output Editor --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Output</span>
                <button onclick="copyOutput()" id="btn-copy-out"
                        class="hidden text-[10px] font-bold px-3 py-1 rounded-lg transition flex items-center gap-1"
                        style="background:rgba(63,185,80,0.12);color:#3fb950;border:1px solid rgba(63,185,80,0.2);">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Copy Output
                </button>
            </div>
            <div style="display:flex;background:#0d1117;border-radius:12px;border:1px solid #30363d;overflow:hidden;font-family:'Fira Code','Cascadia Code',Consolas,monospace;">
                <div id="gutter-out"
                     style="min-width:44px;padding:8px 8px 8px 0;background:#010409;text-align:right;color:#484f58;font-size:11px;line-height:1.7;user-select:none;overflow:hidden;white-space:pre;border-right:1px solid #21262d;">—
</div>
                <textarea id="code-out"
                          readonly
                          placeholder="// Formatted code will appear here..."
                          style="flex:1;min-height:280px;background:transparent;color:#c9d1d9;padding:8px;font-size:11px;line-height:1.7;border:none;outline:none;resize:vertical;tab-size:4;"
                          onscroll="syncGutterScroll(this,'gutter-out')"></textarea>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- Prettier standalone + plugins --}}
<script src="https://cdn.jsdelivr.net/npm/prettier@3/standalone.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prettier@3/plugins/babel.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prettier@3/plugins/html.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prettier@3/plugins/postcss.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prettier@3/plugins/typescript.js"></script>
{{-- SQL Formatter --}}
<script src="https://cdn.jsdelivr.net/npm/sql-formatter@15/dist/sql-formatter.min.js"></script>

<script>
'use strict';

/* ══════════════════════════════════════ IDE Helpers ══════════════════════════════════════ */
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
function updateLineCount() {
    const ta = document.getElementById('code-in');
    const n  = ta.value ? ta.value.split('\n').length : 0;
    document.getElementById('input-lines').textContent = n + ' baris';
}

/* ══════════════════════════════════════ Language Config ══════════════════════════════════════ */
const LANGS = {
    json:       { label: 'JSON',                       color: '#4ec9b0', engine: 'json'       },
    sql:        { label: 'SQL (sql-formatter)',         color: '#569cd6', engine: 'sql'        },
    javascript: { label: 'JavaScript (Prettier)',       color: '#f6c90e', engine: 'prettier',  parser: 'babel'        },
    typescript: { label: 'TypeScript (Prettier)',       color: '#3178c6', engine: 'prettier',  parser: 'typescript'   },
    html:       { label: 'HTML (Prettier)',             color: '#e34c26', engine: 'prettier',  parser: 'html'         },
    css:        { label: 'CSS / SCSS (Prettier)',       color: '#264de4', engine: 'prettier',  parser: 'css'          },
    xml:        { label: 'XML (Custom)',                color: '#f1a65a', engine: 'xml'        },
    php:        { label: 'PHP (Basic Indent)',          color: '#8892be', engine: 'php'        },
};

function onLangChange() {
    const sel  = document.getElementById('lang-select');
    const lang = sel.value;
    const cfg  = LANGS[lang];
    // Color badge
    document.getElementById('lang-badge').style.background = cfg.color;
    document.getElementById('lang-dot').style.background   = cfg.color;
    document.getElementById('lang-name-label').textContent  = cfg.label;
    // Update input placeholder style hint
    document.getElementById('code-in').placeholder = `// Paste your ${lang.toUpperCase()} code here...`;
}
onLangChange();

/* ══════════════════════════════════════ Format Engines ══════════════════════════════════════ */
function formatJSON(code, tabW) {
    const parsed = JSON.parse(code);          // throws on invalid JSON
    return JSON.stringify(parsed, null, tabW);
}

function formatSQL(code, tabW) {
    if (typeof sqlFormatter === 'undefined') throw new Error('sql-formatter belum termuat. Coba refresh halaman.');
    return sqlFormatter.format(code, { language: 'sql', tabWidth: tabW });
}

async function formatPrettier(code, parser, tabW) {
    if (typeof prettier === 'undefined') throw new Error('Prettier belum termuat. Coba refresh halaman.');
    const pluginMap = {
        babel:      window.prettierPlugins?.babel,
        typescript: window.prettierPlugins?.typescript,
        html:       window.prettierPlugins?.html,
        css:        window.prettierPlugins?.postcss,
    };
    const plugin = pluginMap[parser];
    if (!plugin) throw new Error(`Plugin Prettier untuk parser "${parser}" belum termuat.`);
    return await prettier.format(code, { parser, plugins: [plugin], tabWidth: tabW, printWidth: 100 });
}

function formatXML(code) {
    let out = '', depth = 0;
    const tab = '  ';
    const tokens = code
        .replace(/>\s*</g, '><')
        .replace(/</g, '\n<')
        .replace(/>/g, '>\n')
        .split('\n')
        .map(s => s.trim())
        .filter(Boolean);

    tokens.forEach(token => {
        if (token.startsWith('</')) {
            depth = Math.max(0, depth - 1);
            out += tab.repeat(depth) + token + '\n';
        } else if (token.startsWith('<') && !token.startsWith('<?') && !token.startsWith('<!--') && !token.endsWith('/>')) {
            out += tab.repeat(depth) + token + '\n';
            depth++;
        } else {
            out += tab.repeat(depth) + token + '\n';
        }
    });
    return out.trim();
}

function formatPHP(code, tabW) {
    // Basic: re-indent curly-brace blocks
    const tab = ' '.repeat(tabW);
    let out = '', depth = 0;
    code.split('\n').forEach(line => {
        const t = line.trim();
        if (!t) { out += '\n'; return; }
        if (t.startsWith('}') || t.startsWith(')')) depth = Math.max(0, depth - 1);
        out += tab.repeat(depth) + t + '\n';
        const opens  = (t.match(/[{(]/g) || []).length;
        const closes = (t.match(/[})]/g) || []).length;
        depth = Math.max(0, depth + opens - closes);
    });
    return out.trim();
}

/* ══════════════════════════════════════ Main Actions ══════════════════════════════════════ */
async function runFormat() {
    const code  = document.getElementById('code-in').value.trim();
    const lang  = document.getElementById('lang-select').value;
    const tabW  = parseInt(document.getElementById('tab-width').value);
    const cfg   = LANGS[lang];
    const btn   = document.getElementById('btn-format');
    const label = document.getElementById('btn-label');

    if (!code) { showError('Input kosong. Masukkan kode terlebih dahulu.'); return; }

    clearError();
    btn.disabled = true;
    label.textContent = 'Formatting…';
    document.getElementById('lib-loading').classList.add('hidden');

    try {
        let result;
        if (cfg.engine === 'json')    result = formatJSON(code, tabW);
        else if (cfg.engine === 'sql') result = formatSQL(code, tabW);
        else if (cfg.engine === 'prettier') result = await formatPrettier(code, cfg.parser, tabW);
        else if (cfg.engine === 'xml') result = formatXML(code);
        else if (cfg.engine === 'php') result = formatPHP(code, tabW);
        else throw new Error('Engine tidak dikenal.');

        setOutput(result);
    } catch (err) {
        showError(err.message);
        setOutput('');
    }

    btn.disabled = false;
    label.textContent = 'Format';
}

function setOutput(text) {
    const ta   = document.getElementById('code-out');
    const gut  = document.getElementById('gutter-out');
    const copy = document.getElementById('btn-copy-out');

    ta.value = text;
    if (text) {
        const n = (text + '\n').split('\n').length;
        let o = '';
        for (let i = 1; i < n; i++) o += i + '\n';
        gut.textContent = o;
        copy.classList.remove('hidden');
    } else {
        gut.textContent = '—\n';
        copy.classList.add('hidden');
    }
}

function showError(msg) {
    const el = document.getElementById('error-banner');
    document.getElementById('error-text').textContent = msg;
    el.classList.remove('hidden');
}
function clearError() {
    document.getElementById('error-banner').classList.add('hidden');
}

function clearAll() {
    document.getElementById('code-in').value = '';
    syncGutter(document.getElementById('code-in'), 'gutter-in');
    updateLineCount();
    setOutput('');
    clearError();
}

function copyInput() {
    const text = document.getElementById('code-in').value;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => flashBtn('copy-input-btn', 'Copied!'));
}

function copyOutput() {
    const text = document.getElementById('code-out').value;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('btn-copy-out');
        const orig = btn.innerHTML;
        btn.innerHTML = '✓ Copied!';
        setTimeout(() => btn.innerHTML = orig, 1500);
    });
}

/* Keyboard shortcut: Ctrl+Enter to format */
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        runFormat();
    }
});
</script>
@endpush
