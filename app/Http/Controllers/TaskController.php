<?php

namespace App\Http\Controllers;

use App\Models\TaskDetail;
use App\Models\TaskHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the task headers.
     */
    public function index(Request $request)
    {
        $query = TaskHeader::withCount([
            'details',
            'details as completed_count' => fn($q) => $q->where('status', 'Completed'),
        ])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('note', 'like', '%' . $request->search . '%')
                  ->orWhere('user', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('user')) {
            $query->where('user', $request->user);
        }

        $tasks = $query->paginate(10)->withQueryString();

        // Distinct users for filter dropdown
        $users = TaskHeader::select('user')->whereNotNull('user')->distinct()->orderBy('user')->pluck('user');

        return view('Task.index', compact('tasks', 'users'));
    }

    /**
     * Display the calendar of tasks.
     */
    public function calendar(Request $request)
    {
        $month = (int) $request->input('month', date('n'));
        $year  = (int) $request->input('year', date('Y'));

        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        $users = TaskHeader::select('user')->whereNotNull('user')->distinct()->orderBy('user')->pluck('user');

        $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();

        $tasksQuery = TaskHeader::with(['details'])
            ->withCount([
                'details',
                'details as completed_count' => fn($q) => $q->where('status', 'Completed'),
            ])
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);

        if ($request->filled('user')) {
            $tasksQuery->where('user', $request->user);
        }

        $tasks = $tasksQuery->get()->groupBy(fn($task) => $task->date ? $task->date->format('Y-m-d') : '');

        return view('Task.calendar', compact('tasks', 'users', 'month', 'year'));
    }

    /**
     * Show the form for creating a new task header.
     */
    public function create(Request $request)
    {
        $defaultDate = $request->input('date');
        // Distinct users already saved, for autocomplete/options
        $users = TaskHeader::select('user')->whereNotNull('user')->distinct()->orderBy('user')->pluck('user');
        return view('Task.create', compact('users', 'defaultDate'));
    }

    /**
     * Store a newly created task header and its details.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'date'             => 'required|date',
            'path'             => 'nullable|string|max:500',
            'note'             => 'nullable|string',
            'user'             => 'required|string|max:255',
            'details'          => 'nullable|array',
            'details.*.name'   => 'nullable|string|max:255',
            'details.*.desc'   => 'nullable|string',
            'details.*.status' => 'required_with:details|in:Pending,Completed',
        ]);

        DB::transaction(function () use ($request) {
            $header = TaskHeader::create([
                'title' => $request->title,
                'path'  => $request->path,
                'date'  => $request->date,
                'note'  => $request->note,
                'user'  => $request->user,
            ]);

            if ($request->filled('details')) {
                $details = array_filter($request->details, fn($d) => !empty($d['name']));
                foreach ($details as $detail) {
                    $name = $detail['name'];
                    if (!empty($detail['category'])) {
                        $name = strtolower($detail['category']) . '.' . $name;
                    }
                    $header->details()->create([
                        'name'   => $name,
                        'desc'   => $detail['desc'] ?? null,
                        'status' => $detail['status'],
                    ]);
                }
            }
        });

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dibuat.');
    }

    /**
     * Display the specified task header with its details.
     */
    public function show(TaskHeader $task)
    {
        $task->load('details');
        return view('Task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task header.
     */
    public function edit(TaskHeader $task)
    {
        $task->load('details');
        $users = TaskHeader::select('user')->whereNotNull('user')->distinct()->orderBy('user')->pluck('user');
        return view('Task.edit', compact('task', 'users'));
    }

    /**
     * Update the specified task header and synchronize details.
     */
    public function update(Request $request, TaskHeader $task)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'date'             => 'required|date',
            'path'             => 'nullable|string|max:500',
            'note'             => 'nullable|string',
            'user'             => 'required|string|max:255',
            'details'          => 'nullable|array',
            'details.*.id'     => 'nullable|integer|exists:task_details,id',
            'details.*.name'   => 'nullable|string|max:255',
            'details.*.desc'   => 'nullable|string',
            'details.*.status' => 'required_with:details|in:Pending,Completed',
        ]);

        DB::transaction(function () use ($request, $task) {
            $task->update([
                'title' => $request->title,
                'path'  => $request->path,
                'date'  => $request->date,
                'note'  => $request->note,
                'user'  => $request->user,
            ]);

            // Sync details
            $submittedIds = [];
            $details = array_filter($request->input('details', []), fn($d) => !empty($d['name']));

            foreach ($details as $detail) {
                $name = $detail['name'];
                if (!empty($detail['category'])) {
                    $prefix = strtolower($detail['category']) . '.';
                    if (strpos($name, $prefix) !== 0) {
                        $name = $prefix . $name;
                    }
                }
                if (!empty($detail['id'])) {
                    TaskDetail::where('id', $detail['id'])
                        ->where('task_header_id', $task->id)
                        ->update([
                            'name'   => $name,
                            'desc'   => $detail['desc'] ?? null,
                            'status' => $detail['status'],
                        ]);
                    $submittedIds[] = $detail['id'];
                } else {
                    $new = $task->details()->create([
                        'name'   => $name,
                        'desc'   => $detail['desc'] ?? null,
                        'status' => $detail['status'],
                    ]);
                    $submittedIds[] = $new->id;
                }
            }

            // Delete removed detail rows
            $task->details()->whereNotIn('id', $submittedIds)->delete();
        });

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task berhasil diperbarui.');
    }

    /**
     * Remove the specified task header (cascades to details).
     */
    public function destroy(TaskHeader $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus.');
    }
}
