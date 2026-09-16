<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['owner', 'members'])->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::all();

        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'required|exists:users,id',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => $request->owner_id,
        ]);

        $project->members()->syncWithoutDetaching([
            $request->owner_id
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil dibuat.');
    }

    public function show(Project $project)
    {
        $project->load(['owner', 'members']);

        $users = User::whereNotIn(
            'id',
            $project->members->pluck('id')
        )->get();

        return view('projects.show', compact('project', 'users'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }

    public function addMember(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $project->members()->syncWithoutDetaching([
            $request->user_id
        ]);

        return back()->with(
            'success',
            'Anggota berhasil ditambahkan.'
        );
    }

    public function removeMember(Project $project, User $user)
    {
        if ($project->owner_id == $user->id) {
            return back()->with(
                'error',
                'Owner tidak bisa dihapus.'
            );
        }

        $project->members()->detach($user->id);

        return back()->with(
            'success',
            'Anggota berhasil dihapus.'
        );
    }
}