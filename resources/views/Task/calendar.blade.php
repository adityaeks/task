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

    // Global Holiday definitions
    $fixedHolidays = [
        '01-01' => 'Tahun Baru Masehi',
        '05-01' => 'Hari Buruh Internasional',
        '06-01' => 'Hari Lahir Pancasila',
        '08-17' => 'Hari Kemerdekaan RI',
        '12-25' => 'Hari Raya Natal',
    ];
    
    $moveableHolidays2026 = [
        '2026-05-14' => 'Kenaikan Yesus Kristus',
        '2026-05-27' => 'Hari Raya Waisak',
        '2026-06-16' => 'Hari Raya Idul Adha',
        '2026-07-13' => 'Tahun Baru Islam',
        '2026-08-25' => 'Maulid Nabi Muhammad SAW',
    ];

    // Pre-calculate all 12 months for the Yearly Grid view
    $yearlyMonths = [];
    for ($m = 1; $m <= 12; $m++) {
        $firstDayOfMonth = \Carbon\Carbon::create($selectedYear, $m, 1);
        $daysInM = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 = Sunday, 6 = Saturday
        
        $mCells = [];
        // Add empty cells for padding
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $mCells[] = null;
        }
        
        // Add calendar dates
        for ($d = 1; $d <= $daysInM; $d++) {
            $cDate = \Carbon\Carbon::create($selectedYear, $m, $d);
            $dKey = $cDate->format('Y-m-d');
            $mdKey = $cDate->format('m-d');
            
            $isSun = ($cDate->dayOfWeek === 0);
            $isHoli = $isSun 
                || isset($fixedHolidays[$mdKey]) 
                || ($selectedYear === 2026 && isset($moveableHolidays2026[$dKey]));
                
            $holiName = '';
            if (isset($fixedHolidays[$mdKey])) {
                $holiName = $fixedHolidays[$mdKey];
            } elseif ($selectedYear === 2026 && isset($moveableHolidays2026[$dKey])) {
                $holiName = $moveableHolidays2026[$dKey];
            } elseif ($isSun) {
                $holiName = 'Hari Minggu';
            }
            
            $isTod = ($dKey === $todayDateStr);
            
            $mCells[] = [
                'day' => $d,
                'date' => $cDate,
                'isSunday' => $isSun,
                'isHoliday' => $isHoli,
                'holidayName' => $holiName,
                'isToday' => $isTod
            ];
        }
        
        $yearlyMonths[$m] = [
            'name' => $monthsList[$m],
            'cells' => $mCells
        ];
    }

    // Find holidays in the current selected month (excluding Sundays)
    $monthlyHolidays = [];
    foreach ($fixedHolidays as $md => $name) {
        $parts = explode('-', $md);
        if ((int)$parts[0] === $selectedMonth) {
            $date = \Carbon\Carbon::create($selectedYear, (int)$parts[0], (int)$parts[1]);
            $monthlyHolidays[] = [
                'date' => $date,
                'name' => $name
            ];
        }
    }
    if ($selectedYear === 2026) {
        foreach ($moveableHolidays2026 as $dateStr => $name) {
            $date = \Carbon\Carbon::parse($dateStr);
            if ($date->month === $selectedMonth) {
                $monthlyHolidays[] = [
                    'date' => $date,
                    'name' => $name
                ];
            }
        }
    }
    // Sort monthly holidays by date
    usort($monthlyHolidays, function($a, $b) {
        return $a['date']->timestamp <=> $b['date']->timestamp;
    });

    // Find all holidays in the selected year (excluding Sundays)
    $yearlyHolidaysList = [];
    foreach ($fixedHolidays as $md => $name) {
        $parts = explode('-', $md);
        $date = \Carbon\Carbon::create($selectedYear, (int)$parts[0], (int)$parts[1]);
        $yearlyHolidaysList[] = [
            'date' => $date,
            'name' => $name
        ];
    }
    if ($selectedYear === 2026) {
        foreach ($moveableHolidays2026 as $dateStr => $name) {
            $date = \Carbon\Carbon::parse($dateStr);
            $yearlyHolidaysList[] = [
                'date' => $date,
                'name' => $name
            ];
        }
    }
    // Sort yearly holidays by date
    usort($yearlyHolidaysList, function($a, $b) {
        return $a['date']->timestamp <=> $b['date']->timestamp;
    });
@endphp

<style>
    /* Custom CSS to handle robust, subtle hover fallbacks and styling */
    .calendar-cell {
        min-height: 95px !important;
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

    /* Tasks container max-height fallback */
    .calendar-tasks-container {
        max-height: 56px !important;
    }

    /* Robust responsive fallbacks independent of Tailwind compilation on server */
    @media (min-width: 768px) {
        .calendar-desktop-only {
            display: flex !important;
        }
        .calendar-mobile-only {
            display: none !important;
        }
    }
    @media (max-width: 767px) {
        .calendar-desktop-only {
            display: none !important;
        }
        .calendar-mobile-only {
            display: flex !important;
        }
    }

    /* Failsafe Task Badge Styles to bypass Tailwind JIT compilation in production */
    .task-badge {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 4px !important;
        padding: 2px 6px !important;
        border-radius: 6px !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        text-decoration: none !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: transform 0.15s ease, background-color 0.15s ease, border-color 0.15s ease !important;
        overflow: hidden !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }
    .task-badge:hover {
        transform: scale(1.01) !important;
    }

    /* Completed Badge Style (Emerald Theme) */
    .badge-completed {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border: 1px solid rgba(16, 185, 129, 0.2) !important;
    }
    .badge-completed:hover {
        background-color: #d1fae5 !important;
    }
    .dark .badge-completed {
        background-color: rgba(6, 78, 59, 0.25) !important;
        color: #34d399 !important;
        border: 1px solid rgba(6, 78, 59, 0.4) !important;
    }
    .dark .badge-completed:hover {
        background-color: rgba(6, 78, 59, 0.45) !important;
    }

    /* Pending Badge Style (Indigo Theme) */
    .badge-pending {
        background-color: rgba(238, 242, 255, 0.7) !important;
        color: #4338ca !important;
        border: 1px solid rgba(199, 210, 254, 0.3) !important;
    }
    .badge-pending:hover {
        background-color: rgba(224, 231, 255, 0.7) !important;
    }
    .dark .badge-pending {
        background-color: rgba(30, 27, 75, 0.25) !important;
        color: #818cf8 !important;
        border: 1px solid rgba(49, 46, 129, 0.35) !important;
    }
    .dark .badge-pending:hover {
        background-color: rgba(30, 27, 75, 0.45) !important;
    }

    /* Sub-task Progress Counter */
    .task-badge-progress {
        font-size: 8px !important;
        opacity: 0.75 !important;
        font-weight: 600 !important;
        flex-shrink: 0 !important;
    }

    /* Assigned User Initials Pill */
    .task-badge-user {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 2px 4px !important;
        border-radius: 4px !important;
        font-size: 8px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        flex-shrink: 0 !important;
        transform: scale(0.9) !important;
        background-color: rgba(226, 232, 240, 0.6) !important;
        color: #475569 !important;
    }
    .dark .task-badge-user {
        background-color: rgba(39, 39, 42, 0.8) !important;
        color: #a1a1aa !important;
    }

    /* Switcher Button custom styles to bypass production JIT compilation issues */
    .btn-switcher-active {
        background-color: #ffffff !important;
        color: #4f46e5 !important; /* indigo-600 */
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .dark .btn-switcher-active {
        background-color: #3f3f46 !important; /* zinc-700 */
        color: #ffffff !important;
    }
    
    .btn-switcher-inactive {
        background-color: transparent !important;
        color: #475569 !important; /* slate-600 */
    }
    .dark .btn-switcher-inactive {
        color: #a1a1aa !important; /* zinc-400 */
    }
    .btn-switcher-inactive:hover {
        background-color: rgba(226, 232, 240, 0.5) !important; /* slate-200/50 */
    }
    .dark .btn-switcher-inactive:hover {
        background-color: rgba(63, 63, 70, 0.5) !important; /* zinc-700/50 */
    }

    /* Custom styles for Holiday List Items to ensure compatibility without Tailwind JIT */
    .holiday-list-item {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 11px !important;
        line-height: 1.625 !important;
        padding-top: 2px !important;
        padding-bottom: 2px !important;
    }
    
    .holiday-dot {
        display: inline-block !important;
        width: 4px !important;
        height: 4px !important;
        border-radius: 9999px !important;
        background-color: #f43f5e !important; /* rose-500 */
        flex-shrink: 0 !important;
    }
    
    .holiday-date {
        font-weight: 600 !important;
        color: #334155 !important; /* slate-700 */
    }
    .dark .holiday-date {
        color: #d4d4d8 !important; /* zinc-300 */
    }
    
    .holiday-name {
        font-weight: 500 !important;
        color: #e11d48 !important; /* rose-600 */
    }
    .dark .holiday-name {
        color: #fb7185 !important; /* rose-400 */
    }
    
    .holiday-divider {
        margin-left: 6px !important;
        margin-right: 6px !important;
        color: #94a3b8 !important; /* slate-400 */
    }
    .dark .holiday-divider {
        color: #52525b !important; /* zinc-600 */
    }

    /* Failsafe Yearly Month Grid Layout to bypass Tailwind compilation on production */
    .yearly-months-grid {
        display: grid !important;
        grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
        gap: 24px !important;
    }
    @media (min-width: 640px) {
        .yearly-months-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }
    @media (min-width: 1024px) {
        .yearly-months-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }
    @media (min-width: 1200px) {
        .yearly-months-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }
    }

    /* Failsafe Mini Calendar Styles to bypass production Tailwind compilation issues */
    .mini-calendar-header-item {
        font-size: 9px !important;
        font-weight: 800 !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        color: #94a3b8 !important; /* slate-400 */
    }
    .dark .mini-calendar-header-item {
        color: #71717a !important; /* zinc-500 */
    }
    .mini-calendar-header-sunday {
        color: #e11d48 !important; /* rose-600 */
        font-weight: 900 !important;
    }
    .dark .mini-calendar-header-sunday {
        color: #fb7185 !important; /* rose-450 */
    }

    .mini-calendar-date-cell {
        font-size: 10px !important;
        font-weight: 700 !important;
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
            {{-- View Switcher Toggle Group --}}
            <div class="flex items-center bg-slate-100 dark:bg-zinc-800/60 p-1 rounded-xl">
                <button onclick="switchView('monthly')" id="btn-view-monthly" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition duration-150 btn-switcher-active"
                        title="Tampilan Bulanan Detail">
                    Bulanan
                </button>
                <button onclick="switchView('yearly')" id="btn-view-yearly" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition duration-150 btn-switcher-inactive"
                        title="Tampilan Setahun Penuh">
                    Full
                </button>
            </div>

            {{-- Prev/Next Month Steppers (Visible in Monthly View only) --}}
            <div id="monthly-navigation-steppers" class="flex items-center bg-slate-100 dark:bg-zinc-800/60 p-1 rounded-xl">
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

            {{-- Prev/Next Year Steppers (Visible in Yearly View only) --}}
            <div id="yearly-navigation-steppers" class="hidden flex items-center bg-slate-100 dark:bg-zinc-800/60 p-1 rounded-xl">
                <a href="{{ route('tasks.calendar', ['month' => $selectedMonth, 'year' => $selectedYear - 1]) }}" 
                   class="p-1.5 rounded-lg text-slate-600 hover:bg-white dark:text-zinc-400 dark:hover:bg-zinc-700 transition" 
                   title="Tahun Sebelumnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                
                <span class="text-xs font-bold px-3 text-slate-700 dark:text-zinc-300 select-none min-w-[120px] text-center">
                    Tahun {{ $selectedYear }}
                </span>

                <a href="{{ route('tasks.calendar', ['month' => $selectedMonth, 'year' => $selectedYear + 1]) }}" 
                   class="p-1.5 rounded-lg text-slate-600 hover:bg-white dark:text-zinc-400 dark:hover:bg-zinc-700 transition" 
                   title="Tahun Berikutnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Calendar Main Table (Monthly View Wrapper) --}}
    <div id="calendar-monthly-view" class="transition-all duration-300">
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
                            <div class="calendar-desktop-only flex-col gap-1 overflow-y-auto calendar-tasks-container pr-0.5 compact-scrollbar">
                                 @foreach($dayTasks as $task)
                                    @php
                                        $allDone = ($task->details_count > 0 && $task->completed_count === $task->details_count);
                                        $badgeStyle = $allDone ? 'badge-completed' : 'badge-pending';
                                    @endphp
                                    <a href="{{ route('tasks.show', $task->id) }}" 
                                       class="task-badge {{ $badgeStyle }}"
                                       title="{{ $task->title }} ({{ $task->completed_count }}/{{ $task->details_count }} subtask)">
                                        <span class="truncate flex-1">{{ $task->title }}</span>
                                        
                                        {{-- Sub-task Progress Counter --}}
                                        @if($task->details_count > 0)
                                            <span class="task-badge-progress">
                                                {{ $task->completed_count }}/{{ $task->details_count }}
                                            </span>
                                        @endif

                                        {{-- Assigned User indicator --}}
                                        @if($task->user)
                                            <span class="task-badge-user">
                                                {{ substr($task->user, 0, 2) }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>

                            {{-- Mobile Layout: Dot Indicators for task count --}}
                            @if($taskCount > 0)
                                <div class="calendar-mobile-only flex-wrap gap-0.5 justify-center mt-0.5">
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

    {{-- Calendar Yearly View (Full Year Layout) --}}
    <div id="calendar-yearly-view" class="hidden transition-all duration-300">
        <div class="yearly-months-grid">
            @foreach($yearlyMonths as $mNum => $yMonth)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm p-4 flex flex-col justify-between">
                    {{-- Month Name Header (links to that month's detail) --}}
                    <div class="mb-3 text-center border-b border-slate-100 dark:border-zinc-800/40 pb-2">
                        <a href="{{ route('tasks.calendar', ['month' => $mNum, 'year' => $selectedYear]) }}" 
                           class="font-outfit font-extrabold text-sm text-slate-800 dark:text-zinc-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                           title="Lihat detail bulan {{ $yMonth['name'] }}">
                            {{ $yMonth['name'] }}
                        </a>
                    </div>
                    
                    {{-- Mini-calendar weekday headers --}}
                    <div class="calendar-grid-7 gap-y-1 text-center mb-1.5">
                        @php
                            $miniDays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
                        @endphp
                        @foreach($miniDays as $idx => $mDay)
                            <span class="mini-calendar-header-item {{ $idx === 0 ? 'mini-calendar-header-sunday' : '' }}">
                                {{ $mDay }}
                            </span>
                        @endforeach
                    </div>
                    
                    {{-- Mini-calendar cells grid --}}
                    <div class="calendar-grid-7 gap-y-1 text-center">
                        @foreach($yMonth['cells'] as $cell)
                            @if(is_null($cell))
                                <span class="text-[10px] py-0.5 text-transparent select-none"></span>
                            @else
                                @php
                                    $cellDate = $cell['date'];
                                    $dateStr = $cellDate->format('Y-m-d');
                                    $dayTasks = $tasks->get($dateStr) ?? collect();
                                    $taskCount = $dayTasks->count();
                                @endphp
                                <a href="{{ route('tasks.calendar', ['month' => $mNum, 'year' => $selectedYear]) }}"
                                   class="relative group py-0.5 mini-calendar-date-cell rounded-full flex flex-col items-center justify-center h-5 w-5 mx-auto transition-all duration-100
                                       @if($cell['isToday'])
                                           border-2 border-slate-900 dark:border-white font-black text-slate-900 dark:text-white scale-110 shadow-sm
                                       @elseif($cell['isHoliday'])
                                           text-rose-600 dark:text-rose-400 font-extrabold
                                       @else
                                           text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800
                                       @endif"
                                   title="{{ $cellDate->translatedFormat('d F Y') }}{{ $cell['holidayName'] ? ' - ' . $cell['holidayName'] : '' }}{{ $taskCount > 0 ? ' (' . $taskCount . ' task)' : '' }}">
                                   <span>{{ $cell['day'] }}</span>
                                   
                                   {{-- Ultra-minimal subtle task dot under the date cell if it contains tasks --}}
                                   @if($taskCount > 0)
                                       <span class="absolute bottom-[2px] h-[3.5px] w-[3.5px] rounded-full 
                                           @if($cell['isToday']) bg-slate-900 dark:bg-white @elseif($cell['isHoliday']) bg-rose-500 @else bg-indigo-500 @endif"></span>
                                   @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Holiday Information Card (Monthly View) --}}
    <div id="monthly-holidays-card" class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm p-4 lg:p-5 mt-4 transition-all duration-300">
        <div class="flex items-center gap-2 mb-3">
            <span class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-450">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </span>
            <h2 class="font-outfit font-extrabold text-sm tracking-tight text-slate-800 dark:text-zinc-200">Informasi Hari Libur Nasional - {{ $monthsList[$selectedMonth] }} {{ $selectedYear }}</h2>
        </div>
        
        @if(count($monthlyHolidays) > 0)
            <ul class="space-y-2 mt-1 pl-1">
                @foreach($monthlyHolidays as $holi)
                    <li class="holiday-list-item">
                        <span class="holiday-dot"></span>
                        <div>
                            <span class="holiday-date">
                                {{ $holi['date']->translatedFormat('d F Y') }} ({{ $holi['date']->translatedFormat('l') }})
                            </span>
                            <span class="holiday-divider">-</span>
                            <span class="holiday-name">{{ $holi['name'] }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-[11px] text-slate-400 dark:text-zinc-500 font-semibold pl-1">Tidak ada hari libur nasional di bulan ini.</p>
        @endif
    </div>

    {{-- Holiday Information Card (Yearly View) --}}
    <div id="yearly-holidays-card" class="hidden bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/60 dark:border-zinc-800/60 shadow-sm p-4 lg:p-5 mt-4 transition-all duration-300">
        <div class="flex items-center gap-2 mb-3">
            <span class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-450">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </span>
            <h2 class="font-outfit font-extrabold text-sm tracking-tight text-slate-800 dark:text-zinc-200">Daftar Hari Libur Nasional Tahun {{ $selectedYear }}</h2>
        </div>
        
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-6 mt-1 pl-1">
            @foreach($yearlyHolidaysList as $holi)
                <li class="holiday-list-item">
                    <span class="holiday-dot"></span>
                    <div>
                        <span class="holiday-date">
                            {{ $holi['date']->translatedFormat('d F Y') }} ({{ $holi['date']->translatedFormat('l') }})
                        </span>
                        <span class="holiday-divider">-</span>
                        <span class="holiday-name">{{ $holi['name'] }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

</div>

<script>
    // Premium Javascript View Switcher (Bulanan / Full Year)
    function switchView(view) {
        const monthlyView = document.getElementById('calendar-monthly-view');
        const yearlyView = document.getElementById('calendar-yearly-view');
        
        const monthlySteppers = document.getElementById('monthly-navigation-steppers');
        const yearlySteppers = document.getElementById('yearly-navigation-steppers');

        const monthlyHolidays = document.getElementById('monthly-holidays-card');
        const yearlyHolidays = document.getElementById('yearly-holidays-card');
        
        const btnMonthly = document.getElementById('btn-view-monthly');
        const btnYearly = document.getElementById('btn-view-yearly');

        const activeClasses = ['btn-switcher-active'];
        const inactiveClasses = ['btn-switcher-inactive'];

        if (view === 'yearly') {
            // Hide Monthly and Show Yearly grid
            if (monthlyView) monthlyView.classList.add('hidden');
            if (yearlyView) yearlyView.classList.remove('hidden');

            // Switch steppers & holiday cards
            if (monthlySteppers) monthlySteppers.classList.add('hidden');
            if (yearlySteppers) yearlySteppers.classList.remove('hidden');
            if (monthlyHolidays) monthlyHolidays.classList.add('hidden');
            if (yearlyHolidays) yearlyHolidays.classList.remove('hidden');

            // Set buttons active state
            if (btnYearly) {
                activeClasses.forEach(cls => btnYearly.classList.add(cls));
                inactiveClasses.forEach(cls => btnYearly.classList.remove(cls));
            }
            if (btnMonthly) {
                activeClasses.forEach(cls => btnMonthly.classList.remove(cls));
                inactiveClasses.forEach(cls => btnMonthly.classList.add(cls));
            }

            localStorage.setItem('task_calendar_view', 'yearly');
        } else {
            // Show Monthly and Hide Yearly grid
            if (monthlyView) monthlyView.classList.remove('hidden');
            if (yearlyView) yearlyView.classList.add('hidden');

            // Switch steppers & holiday cards
            if (monthlySteppers) monthlySteppers.classList.remove('hidden');
            if (yearlySteppers) yearlySteppers.classList.add('hidden');
            if (monthlyHolidays) monthlyHolidays.classList.remove('hidden');
            if (yearlyHolidays) yearlyHolidays.classList.add('hidden');

            // Set buttons active state
            if (btnMonthly) {
                activeClasses.forEach(cls => btnMonthly.classList.add(cls));
                inactiveClasses.forEach(cls => btnMonthly.classList.remove(cls));
            }
            if (btnYearly) {
                activeClasses.forEach(cls => btnYearly.classList.remove(cls));
                inactiveClasses.forEach(cls => btnYearly.classList.add(cls));
            }

            localStorage.setItem('task_calendar_view', 'monthly');
        }
    }

    // Persist and load view choice on page load - Always default to monthly on new visit/refresh
    document.addEventListener('DOMContentLoaded', function() {
        switchView('monthly');
    });
</script>
@endsection
