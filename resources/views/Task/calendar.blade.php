@extends('layouts.app')

@section('title', 'Kalender Task | TaskManager')

@section('content')
@php
    // Calculate calendar dates
    $selectedMonth = (int) $month;
    $selectedYear = (int) $year;

    // First day of selected month
    $firstDay = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1);
    
    // Previous and Next month info
    $prevMonth = (clone $firstDay)->subMonth();
    $nextMonth = (clone $firstDay)->addMonth();

    // Day of the week the month starts on (0 for Sunday, 6 for Saturday)
    $startOfWeekDay = $firstDay->dayOfWeek;

    // Total days in the current month
    $daysInMonth = $firstDay->daysInMonth;

    // Total days in the previous month
    $daysInPrevMonth = $prevMonth->daysInMonth;

    // Year and Month list for selectors
    $yearsRange = range(date('Y') - 3, date('Y') + 3);
    $monthsList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // Generate grid cells (always 42 cells / 6 weeks to ensure constant grid height)
    $cells = [];

    // Add trailing days from previous month
    for ($i = $startOfWeekDay - 1; $i >= 0; $i--) {
        $dayNum = $daysInPrevMonth - $i;
        $cells[] = [
            'day' => $dayNum,
            'date' => \Carbon\Carbon::create($prevMonth->year, $prevMonth->month, $dayNum),
            'isCurrentMonth' => false
        ];
    }

    // Add current month days
    for ($dayNum = 1; $dayNum <= $daysInMonth; $dayNum++) {
        $cells[] = [
            'day' => $dayNum,
            'date' => \Carbon\Carbon::create($selectedYear, $selectedMonth, $dayNum),
            'isCurrentMonth' => true
        ];
    }

    // Add leading days from next month until 42 cells
    $nextMonthDay = 1;
    while (count($cells) < 42) {
        $cells[] = [
            'day' => $nextMonthDay,
            'date' => \Carbon\Carbon::create($nextMonth->year, $nextMonth->month, $nextMonthDay),
            'isCurrentMonth' => false
        ];
        $nextMonthDay++;
    }

    $todayDateStr = date('Y-m-d');
@endphp

<style>
    /* Custom CSS to handle robust, subtle hover fallbacks and styling */
    .calendar-cell {
        transition: background-color 0.1s ease, border-color 0.1s ease;
    }
    
    /* Base background colors to avoid Tailwind slash opacity fallback issues */
    .calendar-cell.cell-current {
        background-color: #ffffff !important;
    }
    .dark .calendar-cell.cell-current {
        background-color: #18181b !important; /* zinc-900 */
    }
    
    .calendar-cell.cell-outside {
        background-color: #f8fafc !important; /* slate-50 */
    }
    .dark .calendar-cell.cell-outside {
        background-color: #09090b !important; /* zinc-950 (perfect pitch dark) */
    }
    
    /* Light mode: extremely subtle hover highlight */
    .calendar-cell.cell-current:hover {
        background-color: #f1f5f9 !important; /* slate-100 */
    }
    /* Dark mode: extremely subtle hover highlight */
    .dark .calendar-cell.cell-current:hover {
        background-color: #27272a !important; /* zinc-800 */
    }

    /* Outside Month Cells Hover */
    .calendar-cell.cell-outside:hover {
        background-color: #e2e8f0 !important; /* slate-200 */
    }
    .dark .calendar-cell.cell-outside:hover {
        background-color: #18181b !important; /* zinc-900 */
    }

    /* Compact scrollbar styling for cell content */
    .compact-scrollbar::-webkit-scrollbar {
        width: 3px;
        height: 3px;
    }
    .compact-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 9999px;
    }
    .dark .compact-scrollbar::-webkit-scrollbar-thumb {
        background-color: #3f3f46;
    }
    .compact-scrollbar::-webkit-scrollbar-track {
        background-color: transparent;
    }

    /* Failsafe Grid 7 columns fallback in case Tailwind compiled assets aren't updated on server */
    .calendar-grid-7 {
        display: grid !important;
        grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
        gap: 1px !important;
    }
</style>

<div class="space-y-4">

    {{-- Page Header & Navigations --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-zinc-900 p-4 lg:p-5 rounded-2xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm">
        <div>
            <h1 class="font-outfit font-extrabold text-xl tracking-tight">Kalender Task</h1>
            <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">Pantau agenda kerja dan status penyelesaian tugas secara real-time.</p>
        </div>

        {{-- Interactive Filters --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Prev/Next Month Steppers --}}
            <div class="flex items-center bg-slate-100 dark:bg-zinc-800/60 p-1 rounded-xl">
                <a href="{{ route('tasks.calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" 
                   class="p-1.5 rounded-lg text-slate-600 hover:bg-white dark:text-zinc-400 dark:hover:bg-zinc-700 transition" 
                   title="Bulan Sebelumnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                
                <span class="text-xs font-bold px-3 text-slate-700 dark:text-zinc-300 select-none min-w-[120px] text-center">
                    {{ $monthsList[$selectedMonth] }} {{ $selectedYear }}
                </span>

                <a href="{{ route('tasks.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" 
                   class="p-1.5 rounded-lg text-slate-600 hover:bg-white dark:text-zinc-400 dark:hover:bg-zinc-700 transition" 
                   title="Bulan Berikutnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Calendar Main Table --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm overflow-hidden">
        
        {{-- Weekday Headers --}}
        <div class="grid grid-cols-7 calendar-grid-7 border-b border-slate-100 dark:border-zinc-800/60 bg-slate-50/60 dark:bg-zinc-900/50">
            @php
                $weekDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            @endphp
            @foreach($weekDays as $index => $day)
                <div class="px-2 py-2 text-center text-[10px] font-extrabold uppercase tracking-wider {{ $index === 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-zinc-500' }}">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- Monthly Grid Cells --}}
        <div class="grid grid-cols-7 calendar-grid-7 bg-slate-100 dark:bg-zinc-850">
            @foreach($cells as $cell)
                @php
                    $cellDate = $cell['date'];
                    $dateKey = $cellDate->format('Y-m-d');
                    $isCurrentMonth = $cell['isCurrentMonth'];
                    $dayTasks = $tasks->get($dateKey) ?? collect();
                    $isToday = ($dateKey === $todayDateStr);
                    $taskCount = $dayTasks->count();

                    // Holiday check logic (Every Sunday + fixed/moveable Indonesian national holidays)
                    $isSunday = $cellDate->isSunday();
                    
                    // Fixed annual national holidays
                    $fixedHolidays = [
                        '01-01' => 'Tahun Baru Masehi',
                        '05-01' => 'Hari Buruh Internasional',
                        '06-01' => 'Hari Lahir Pancasila',
                        '08-17' => 'Hari Kemerdekaan RI',
                        '12-25' => 'Hari Raya Natal',
                    ];
                    
                    // Year-specific moveable holidays for 2026 (matching your calendar image exactly)
                    $moveableHolidays2026 = [
                        '2026-05-14' => 'Kenaikan Yesus Kristus',
                        '2026-05-27' => 'Hari Raya Waisak',
                        '2026-06-16' => 'Hari Raya Idul Adha',
                        '2026-07-13' => 'Tahun Baru Islam',
                        '2026-08-25' => 'Maulid Nabi Muhammad SAW',
                    ];
                    
                    $mdKey = $cellDate->format('m-d');
                    $isHoliday = $isSunday 
                        || isset($fixedHolidays[$mdKey]) 
                        || ($cellDate->year === 2026 && isset($moveableHolidays2026[$dateKey]));
                        
                    $holidayName = '';
                    if (isset($fixedHolidays[$mdKey])) {
                        $holidayName = $fixedHolidays[$mdKey];
                    } elseif ($cellDate->year === 2026 && isset($moveableHolidays2026[$dateKey])) {
                        $holidayName = $moveableHolidays2026[$dateKey];
                    } elseif ($isSunday) {
                        $holidayName = 'Hari Minggu';
                    }
                @endphp

                <div class="relative min-h-[95px] p-1.5 flex flex-col justify-between group calendar-cell transition duration-150 {{ $isCurrentMonth ? 'cell-current text-slate-700 dark:text-zinc-300' : 'cell-outside text-slate-400 dark:text-zinc-500' }}">
                    
                    {{-- Cell Header: Day Number and Action Buttons --}}
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[11px] font-bold rounded-full flex items-center justify-center h-5 w-5
                            @if($isToday)
                                bg-indigo-600 dark:bg-indigo-500 text-white font-extrabold
                            @elseif($isHoliday)
                                {{ $isCurrentMonth ? 'text-rose-600 dark:text-rose-400 font-extrabold' : 'text-rose-300/60 dark:text-rose-900/40 font-semibold' }}
                            @else
                                {{ $isCurrentMonth ? 'text-slate-700 dark:text-zinc-300' : 'text-slate-400/80 dark:text-zinc-600' }}
                            @endif"
                            @if($isHoliday && $holidayName) title="{{ $holidayName }}" @endif>
                            {{ $cell['day'] }}
                        </span>

                        {{-- Quick Add Task button (shows on hover) --}}
                        <a href="{{ route('tasks.create', ['date' => $dateKey]) }}"
                           class="opacity-0 group-hover:opacity-100 transition-opacity duration-150 p-0.5 rounded bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400"
                           title="Tambah Task untuk tanggal ini">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </a>
                    </div>

                    {{-- Cell Content: Tasks Container --}}
                    <div class="flex-1 flex flex-col justify-end space-y-1">
                        {{-- Desktop Layout: Full Text Badges --}}
                        <div class="hidden md:flex flex-col gap-1 overflow-y-auto max-h-[56px] pr-0.5 compact-scrollbar">
                            @foreach($dayTasks as $task)
                                @php
                                    $allDone = ($task->details_count > 0 && $task->completed_count === $task->details_count);
                                    
                                    $badgeStyle = $allDone 
                                        ? 'bg-emerald-50 hover:bg-emerald-100/80 dark:bg-emerald-950/20 dark:hover:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100/60 dark:border-emerald-900/30' 
                                        : 'bg-indigo-50/70 hover:bg-indigo-100/70 dark:bg-indigo-950/20 dark:hover:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 border border-indigo-100/30 dark:border-indigo-900/30';
                                @endphp
                                <a href="{{ route('tasks.show', $task->id) }}" 
                                   class="flex items-center justify-between gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold truncate transition duration-150 transform hover:scale-[1.01] shadow-sm {{ $badgeStyle }}"
                                   title="{{ $task->title }} ({{ $task->completed_count }}/{{ $task->details_count }} subtask)">
                                    <span class="truncate flex-1">{{ $task->title }}</span>
                                    
                                    {{-- Sub-task Progress Counter --}}
                                    @if($task->details_count > 0)
                                        <span class="text-[8px] opacity-75 font-semibold shrink-0">
                                            {{ $task->completed_count }}/{{ $task->details_count }}
                                        </span>
                                    @endif

                                    {{-- Assigned User indicator --}}
                                    @if($task->user)
                                        <span class="px-1.5 py-0.5 bg-slate-200/60 dark:bg-zinc-800/80 rounded-md text-[8px] text-slate-600 dark:text-zinc-400 font-extrabold uppercase tracking-wide shrink-0 scale-90">
                                            {{ substr($task->user, 0, 2) }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        {{-- Mobile Layout: Dot Indicators for task count --}}
                        @if($taskCount > 0)
                            <div class="flex md:hidden flex-wrap gap-0.5 justify-center mt-0.5">
                                @foreach($dayTasks as $task)
                                    @php
                                        $allDone = ($task->details_count > 0 && $task->completed_count === $task->details_count);
                                        $dotColor = $allDone ? 'bg-emerald-500' : 'bg-indigo-500';
                                    @endphp
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dotColor }}" title="{{ $task->title }}"></span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

    </div>

</div>
@endsection
