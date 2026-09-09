<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Tampilkan semua task yang bisa diakses user yang sedang login.
     */
    public function index()
    {
        $tasks = Task::with(['project', 'creator', 'assignees'])
            ->where(function ($query) {
                $query->where('created_by', auth()->id())
                    ->orWhereHas('project', function ($q) {
                        $q->where('owner_id', auth()->id())
                            ->orWhereHas('members', fn ($m) => $m->where('users.id', auth()->id()));
                    });
            })
            ->latest()
            ->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Tampilkan form buat task baru.
     */
    public function create()
    {
        // Ambil project yang dimiliki atau diikuti user
        $projects = Project::where('owner_id', auth()->id())
            ->orWhereHas('members', fn ($q) => $q->where('users.id', auth()->id()))
            ->get();

        return view('tasks.create', compact('projects'));
    }

    /**
     * Simpan task baru ke database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => ['required', 'exists:projects,id'],
            'title'       => ['required', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['required', 'in:low,medium,high'],
            'status'      => ['required', 'in:todo,in_progress,done'],
            'due_date'    => ['nullable', 'date'],
        ]);

        $data['created_by'] = auth()->id();

        $task = Task::create($data);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task berhasil dibuat.');
    }

    /**
     * Tampilkan detail satu task.
     */
    public function show(Task $task)
    {
        $this->authorizeTaskAccess($task);

        $task->load(['project', 'creator', 'assignees']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Tampilkan form edit task.
     */
    public function edit(Task $task)
    {
        $this->authorizeTaskAccess($task);

        $projects = Project::where('owner_id', auth()->id())
            ->orWhereHas('members', fn ($q) => $q->where('users.id', auth()->id()))
            ->get();

        return view('tasks.edit', compact('task', 'projects'));
    }

    /**
     * Update task di database.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($task);

        $data = $request->validate([
            'project_id'  => ['required', 'exists:projects,id'],
            'title'       => ['required', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['required', 'in:low,medium,high'],
            'status'      => ['required', 'in:todo,in_progress,done'],
            'due_date'    => ['nullable', 'date'],
        ]);

        $task->update($data);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task berhasil diperbarui.');
    }

    /**
     * Hapus task dari database.
     */
    public function destroy(Task $task)
    {
        $this->authorizeTaskAccess($task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus.');
    }

    /**
     * Update status task (todo / in_progress / done).
     */
    public function updateStatus(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($task);

        $data = $request->validate([
            'status' => ['required', 'in:todo,in_progress,done'],
        ]);

        $task->update($data);

        return redirect()->back()->with('success', 'Status task diperbarui.');
    }

    /**
     * Cek apakah user berhak mengakses / mengubah task ini.
     * User boleh jika: creator, owner project, atau anggota project.
     */
    private function authorizeTaskAccess(Task $task): void
    {
        $task->loadMissing(['project.members']);

        $isCreator  = $task->created_by === auth()->id();
        $isOwner    = $task->project->owner_id === auth()->id();
        $isMember   = $task->project->members->contains('id', auth()->id());

        abort_unless($isCreator || $isOwner || $isMember, 403);
    }
}
