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
     * Show the form for creating a new task header.
     */
    public function create()
    {
        // Distinct users already saved, for autocomplete/options
        $users = TaskHeader::select('user')->whereNotNull('user')->distinct()->orderBy('user')->pluck('user');
        return view('Task.create', compact('users'));
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
            'user'             => 'nullable|string|max:255',
            'details'          => 'nullable|array',
            'details.*.name'   => 'required_with:details|string|max:255',
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
                    $header->details()->create([
                        'name'   => $detail['name'],
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
            'user'             => 'nullable|string|max:255',
            'details'          => 'nullable|array',
            'details.*.id'     => 'nullable|integer|exists:task_details,id',
            'details.*.name'   => 'required_with:details|string|max:255',
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
                if (!empty($detail['id'])) {
                    TaskDetail::where('id', $detail['id'])
                        ->where('task_header_id', $task->id)
                        ->update([
                            'name'   => $detail['name'],
                            'desc'   => $detail['desc'] ?? null,
                            'status' => $detail['status'],
                        ]);
                    $submittedIds[] = $detail['id'];
                } else {
                    $new = $task->details()->create([
                        'name'   => $detail['name'],
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
