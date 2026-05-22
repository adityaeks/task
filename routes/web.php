<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DiffCheckerController;
use App\Http\Controllers\PrettifierController;
use App\Models\TaskHeader;
use App\Models\TaskDetail;
use App\Models\Note;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    // Recent 5 tasks
    $recentTasks = TaskHeader::withCount([
        'details',
        'details as completed_count' => fn($q) => $q->where('status', 'Completed'),
    ])->latest()->limit(5)->get();

    // Pinned notes
    $pinnedNotes = Note::where('is_pinned', true)->latest()->get();

    return view('dashboard', compact(
        'recentTasks',
        'pinnedNotes'
    ));
})->name('dashboard');


Route::get('tasks/calendar', [TaskController::class, 'calendar'])->name('tasks.calendar');
Route::resource('tasks', TaskController::class);

Route::get('/diff-checker', [DiffCheckerController::class, 'index'])->name('diff.index');

Route::get('/prettifier', [PrettifierController::class, 'index'])->name('prettifier.index');

// Notes Routes
use App\Http\Controllers\NoteController;
Route::resource('notes', NoteController::class)->except(['create', 'show', 'edit']);
Route::patch('notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.pin');
Route::patch('notes/{note}/color', [NoteController::class, 'updateColor'])->name('notes.color');

// Toggle TaskDetail status route
Route::patch('tasks/details/{detail}/toggle', function (TaskDetail $detail) {
    $detail->status = $detail->status === 'Completed' ? 'Pending' : 'Completed';
    $detail->save();

    $task = $detail->header;
    $detailsCount = $task->details->count();
    $completedCount = $task->details->where('status', 'Completed')->count();
    $progressPct = $detailsCount > 0 ? round(($completedCount / $detailsCount) * 100) : 0;

    return response()->json([
        'success' => true,
        'status' => $detail->status,
        'progressPct' => $progressPct,
        'completedCount' => $completedCount,
        'detailsCount' => $detailsCount
    ]);
})->name('tasks.details.toggle');

