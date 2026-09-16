<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskListRequest;
use App\Models\TaskList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskListController extends Controller
{
    /**
     * Tampilkan daftar tugas yang dimiliki atau diikuti user yang sedang login.
     */
    public function index(): View
    {
        $taskLists = TaskList::withCount('members')
            ->where('owner_id', auth()->id())
            ->orWhereHas('members', fn ($query) => $query->where('users.id', auth()->id()))
            ->latest()
            ->get();

        return view('task-lists.index', compact('taskLists'));
    }

    /**
     * Tampilkan form buat daftar tugas baru.
     */
    public function create(): View
    {
        return view('task-lists.create');
    }

    /**
     * Simpan daftar tugas baru, dengan pembuatnya sebagai pemilik.
     */
    public function store(StoreTaskListRequest $request): RedirectResponse
    {
        $taskList = DB::transaction(function () use ($request) {
            // owner_id diambil dari sesi login di server, TIDAK PERNAH dari request.
            $taskList = new TaskList($request->validated());
            $taskList->owner_id = auth()->id();
            $taskList->save();

            $taskList->members()->attach(auth()->id());

            return $taskList;
        });

        return redirect()->route('task-lists.show', $taskList)
            ->with('success', 'Daftar tugas berhasil dibuat.');
    }

    /**
     * Tampilkan detail satu daftar tugas.
     */
    public function show(TaskList $taskList): View
    {
        $taskList->load(['owner', 'members']);

        return view('task-lists.show', compact('taskList'));
    }
}
