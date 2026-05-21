<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the notes.
     */
    public function index()
    {
        $pinnedNotes = Note::where('is_pinned', true)->latest()->get();
        $otherNotes  = Note::where('is_pinned', false)->latest()->get();

        return view('notes.index', compact('pinnedNotes', 'otherNotes'));
    }

    /**
     * Store a newly created note.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'nullable|string|max:255',
            'content'   => 'required|string',
            'color'     => 'nullable|string|max:50',
            'is_pinned' => 'nullable|boolean',
        ]);

        $note = Note::create([
            'title'     => $request->title,
            'content'   => $request->content,
            'color'     => $request->color ?? '#ffffff',
            'is_pinned' => $request->has('is_pinned') ? (bool)$request->is_pinned : false,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil dibuat.',
                'note'    => $note,
            ]);
        }

        return redirect()->route('notes.index')->with('success', 'Catatan berhasil dibuat.');
    }

    /**
     * Update the specified note.
     */
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title'     => 'nullable|string|max:255',
            'content'   => 'required|string',
            'color'     => 'nullable|string|max:50',
            'is_pinned' => 'nullable|boolean',
        ]);

        $note->update([
            'title'     => $request->title,
            'content'   => $request->content,
            'color'     => $request->color ?? $note->color,
            'is_pinned' => $request->has('is_pinned') ? (bool)$request->is_pinned : $note->is_pinned,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil diperbarui.',
                'note'    => $note,
            ]);
        }

        return redirect()->route('notes.index')->with('success', 'Catatan berhasil diperbarui.');
    }

    /**
     * Toggle the pin status of the note.
     */
    public function togglePin(Note $note)
    {
        $note->update([
            'is_pinned' => !$note->is_pinned,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => $note->is_pinned ? 'Catatan disematkan.' : 'Sematkan catatan dilepas.',
            'is_pinned' => $note->is_pinned,
            'note'      => $note,
        ]);
    }

    /**
     * Update the color of the note.
     */
    public function updateColor(Request $request, Note $note)
    {
        $request->validate([
            'color' => 'required|string|max:50',
        ]);

        $note->update([
            'color' => $request->color,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Warna catatan diperbarui.',
            'color'   => $note->color,
        ]);
    }

    /**
     * Remove the specified note.
     */
    public function destroy(Note $note)
    {
        $note->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil dihapus.',
            ]);
        }

        return redirect()->route('notes.index')->with('success', 'Catatan berhasil dihapus.');
    }
}
