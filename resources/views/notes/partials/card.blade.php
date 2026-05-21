@php
    $isPinned = $note->is_pinned;
    $isLightWhite = ($note->color === '#ffffff' || !$note->color);
    $cardClasses = $isLightWhite 
        ? 'bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 text-slate-800 dark:text-zinc-100'
        : 'border border-transparent text-slate-900';
    $cardStyle = $isLightWhite ? '' : 'background-color: ' . $note->color . ';';

    // Icon Contrast Colors
    $iconColorClass = $isLightWhite 
        ? 'text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200' 
        : 'text-slate-800/60 hover:text-slate-900';
    
    $pinIconColorClass = $isPinned 
        ? ($isLightWhite ? 'text-indigo-600 dark:text-indigo-400' : 'text-indigo-900') 
        : ($isLightWhite ? 'text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-200' : 'text-slate-800/60 hover:text-slate-900');

    // Parse content
    $rawContent = $note->content;
    $lines = explode("\n", e($rawContent));

    if (!function_exists('parseMarkdownInline')) {
        function parseMarkdownInline($text) {
            $text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-extrabold">$1</strong>', $text);
            $text = preg_replace('/\*(.*?)\*/', '<em class="italic">$1</em>', $text);
            return $text;
        }
    }

    $parsedLines = [];
    foreach ($lines as $line) {
        if (preg_match('/^\[\s\]\s(.*)/', $line, $m)) {
            $parsedText = parseMarkdownInline($m[1]);
            $parsedLines[] = '<span class="flex items-center gap-1.5 py-0.5"><input type="checkbox" disabled class="rounded border-slate-300 dark:border-zinc-700 text-indigo-600 focus:ring-0 w-3.5 h-3.5 pointer-events-none"> <span class="' . ($isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800') . '">' . $parsedText . '</span></span>';
        } 
        elseif (preg_match('/^\[x\]\s(.*)/i', $line, $m)) {
            $parsedText = parseMarkdownInline($m[1]);
            $parsedLines[] = '<span class="flex items-center gap-1.5 py-0.5 line-through opacity-60"><input type="checkbox" checked disabled class="rounded border-slate-300 dark:border-zinc-700 text-indigo-600 focus:ring-0 w-3.5 h-3.5 pointer-events-none"> <span class="' . ($isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800') . '">' . $parsedText . '</span></span>';
        }
        elseif (preg_match('/^-\s(.*)/', $line, $m)) {
            $parsedText = parseMarkdownInline($m[1]);
            $parsedLines[] = '<span class="flex items-start gap-1.5 py-0.5"><span class="' . ($isLightWhite ? 'text-indigo-500' : 'text-indigo-900') . ' mt-1.5 shrink-0 w-1.5 h-1.5 rounded-full bg-current"></span> <span class="' . ($isLightWhite ? 'text-slate-600 dark:text-zinc-300' : 'text-slate-800') . '">' . $parsedText . '</span></span>';
        }
        else {
            $parsedText = parseMarkdownInline($line);
            $parsedLines[] = '<div>' . ($parsedText === '' ? '&nbsp;' : $parsedText) . '</div>';
        }
    }
    $contentHtml = implode('', $parsedLines);
@endphp

<div id="note-{{ $note->id }}" data-id="{{ $note->id }}" data-color="{{ $note->color ?? '#ffffff' }}" data-pinned="{{ $isPinned ? '1' : '0' }}" data-raw-content="{{ $rawContent }}"
     class="note-card rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 relative group flex flex-col justify-between {{ $cardClasses }}"
     style="{{ $cardStyle }}">
    
    <div>
        {{-- Header/Pin --}}
        <div class="flex items-start justify-between gap-2 mb-1.5">
            <h4 class="note-title font-bold text-xs leading-snug truncate cursor-pointer" onclick="openEditModal({{ $note->id }})">
                {{ $note->title }}
            </h4>
            <button onclick="togglePin({{ $note->id }})" class="pin-icon {{ $pinIconColorClass }} opacity-0 group-hover:opacity-100 transition-all p-1" title="Sematkan">
                <svg class="w-4 h-4 {{ $isPinned ? 'fill-current rotate-0' : 'fill-none -rotate-45' }} transition-all duration-300" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="17" x2="12" y2="22"></line>
                    <path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.24V5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v4.24c0 .43-.14.85-.4 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24Z"></path>
                </svg>
            </button>
        </div>

        {{-- Content --}}
        <div class="note-content text-[11px] font-medium leading-relaxed cursor-pointer max-h-40 overflow-hidden" onclick="openEditModal({{ $note->id }})">
            {!! $contentHtml !!}
        </div>
    </div>

    {{-- Card Action Toolbar --}}
    <div class="flex items-center justify-end gap-2 mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
        {{-- Color Palette dropdown button --}}
        <div class="relative group/card-palette">
            <button class="card-palette-btn {{ $iconColorClass }} transition-colors p-1" title="Ubah warna">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
            </button>
            <div class="absolute bottom-full right-0 mb-1 hidden group-focus-within/card-palette:flex group-hover/card-palette:flex flex-wrap gap-1 bg-white dark:bg-zinc-800 p-1.5 rounded-xl border border-slate-200/80 dark:border-zinc-700 shadow-xl z-20 w-36">
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
                    <button type="button" onclick="changeColor({{ $note->id }}, '{{ $hex }}')" class="w-5 h-5 rounded-full border border-slate-200 dark:border-zinc-700/60 transition hover:scale-110" style="background-color: {{ $hex }};" title="{{ $name }}"></button>
                @endforeach
            </div>
        </div>
        {{-- Delete Button --}}
        <button onclick="deleteNote({{ $note->id }})" class="card-delete-btn {{ $iconColorClass }} hover:text-rose-500 transition-colors p-1" title="Hapus catatan">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </div>

</div>
