<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Logika Task (disederhanakan):
     * - Pembuat task (created_by) adalah OWNER.
     * - Owner bisa menambahkan user lain sebagai ANGGOTA (pivot task_user),
     *   sehingga task itu juga muncul di daftar tugas mereka.
     * - Hanya owner yang bisa edit / hapus task / kelola anggota.
     * - Anggota hanya bisa melihat + ubah status.
     */

    /**
     * Tampilkan semua task milik user (sebagai owner atau anggota).
     */
    public function index()
    {
        $tasks = Task::with(['project', 'creator', 'assignees'])
            ->where(function ($query) {
                $query->where('created_by', auth()->id())
                    ->orWhereHas('assignees', fn ($q) => $q->where('users.id', auth()->id()));
            })
            ->latest()
            ->get();

        $projects = Project::where('owner_id', auth()->id())
            ->orWhereHas('members', fn ($q) => $q->where('users.id', auth()->id()))
            ->get();

        return view('tasks.index', compact('tasks', 'projects'));
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
     * Simpan task baru. Pembuat otomatis jadi owner + anggota.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => ['required', 'exists:projects,id'],
            'title'       => ['required', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['sometimes', 'in:low,medium,high'],
            'status'      => ['sometimes', 'in:todo,in_progress,done'],
            'due_date'    => ['nullable', 'date'],
        ]);

        $data['priority'] ??= 'medium';
        $data['status'] ??= 'todo';
        $data['created_by'] = auth()->id();

        $task = DB::transaction(function () use ($data) {
            $task = Task::create($data);
            // Owner otomatis jadi anggota supaya task selalu muncul di daftarnya.
            $task->assignees()->syncWithoutDetaching([$task->created_by]);

            return $task;
        });

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dibuat.');
    }

    /**
     * Tampilkan detail satu task (owner maupun anggota boleh).
     */
    public function show(Task $task)
    {
        $this->authorizeTaskAccess($task);

        $task->load(['project', 'creator', 'assignees']);

        // Kandidat anggota baru: semua user yang belum jadi assignee.
        $candidates = User::whereNotIn('id', $task->assignees->pluck('id'))->orderBy('name')->get();

        return view('tasks.show', compact('task', 'candidates'));
    }

    /**
     * Tampilkan form edit task (hanya owner).
     */
    public function edit(Task $task)
    {
        $this->authorizeTaskOwner($task);

        $projects = Project::where('owner_id', auth()->id())
            ->orWhereHas('members', fn ($q) => $q->where('users.id', auth()->id()))
            ->get();

        return view('tasks.edit', compact('task', 'projects'));
    }

    /**
     * Update task di database (hanya owner).
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeTaskOwner($task);

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
     * Hapus task + seluruh keanggotaannya (hanya owner).
     * Pivot ikut terhapus via FK cascadeOnDelete, dibungkus transaksi
     * agar atomik.
     */
    public function destroy(Task $task)
    {
        $this->authorizeTaskOwner($task);

        DB::transaction(function () use ($task) {
            $task->assignees()->detach();
            $task->delete();
        });

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus dari semua anggota.');
    }

    /**
     * Update status task (owner maupun anggota boleh).
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
     * Tambahkan user lain sebagai anggota task (hanya owner).
     * Setelah ditambahkan, task muncul juga di daftar tugas mereka.
     */
    public function addMember(Request $request, Task $task)
    {
        $this->authorizeTaskOwner($task);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $task->assignees()->syncWithoutDetaching([$data['user_id']]);

        return redirect()->back()->with('success', 'Anggota berhasil ditambahkan ke task.');
    }

    /**
     * Hapus anggota dari task (hanya owner). Owner tidak bisa dihapus.
     */
    public function removeMember(Task $task, User $user)
    {
        $this->authorizeTaskOwner($task);

        if ($user->id === $task->created_by) {
            return redirect()->back()->with('error', 'Owner tidak bisa dihapus dari task.');
        }

        $task->assignees()->detach($user->id);

        return redirect()->back()->with('success', 'Anggota berhasil dihapus dari task.');
    }

    /**
     * Akses baca/ubah status: owner ATAU anggota.
     */
    private function authorizeTaskAccess(Task $task): void
    {
        $isOwner = $task->created_by === auth()->id();
        $isMember = $task->assignees()->where('users.id', auth()->id())->exists();

        abort_unless($isOwner || $isMember, 403);
    }

    /**
     * Aksi owner-only: edit, hapus, kelola anggota.
     */
    private function authorizeTaskOwner(Task $task): void
    {
        abort_unless($task->created_by === auth()->id(), 403);
    }
}
