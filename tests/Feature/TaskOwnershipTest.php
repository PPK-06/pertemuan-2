<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeTaskOwner(array $attrs = []): array
{
    $owner = User::factory()->create();
    $project = Project::create(array_merge([
        'name' => 'Project Demo',
        'description' => null,
        'owner_id' => $owner->id,
    ], $attrs));

    $task = Task::create([
        'project_id' => $project->id,
        'created_by' => $owner->id,
        'title' => 'Task Demo',
        'description' => null,
        'priority' => 'medium',
        'status' => 'todo',
    ]);
    $task->assignees()->attach($owner->id);

    return [$owner, $project, $task];
}

it('pembuat task otomatis jadi owner sekaligus anggota', function () {
    $owner = User::factory()->create();
    $project = Project::create([
        'name' => 'Project Demo',
        'owner_id' => $owner->id,
    ]);

    $this->actingAs($owner)->post(route('tasks.store'), [
        'project_id' => $project->id,
        'title' => 'Task Baru',
    ])->assertRedirect(route('tasks.index'));

    $task = Task::first();

    expect($task->created_by)->toBe($owner->id)
        ->and($task->assignees->pluck('id')->all())->toContain($owner->id);
});

it('owner bisa menambahkan anggota sehingga task muncul di daftar mereka', function () {
    [$owner, $project, $task] = makeTaskOwner();
    $member = User::factory()->create();

    $this->actingAs($owner)
        ->post(route('tasks.members.add', $task), ['user_id' => $member->id])
        ->assertRedirect();

    expect($task->fresh()->assignees->pluck('id')->all())->toContain($member->id);

    $this->actingAs($member)->get(route('tasks.index'))
        ->assertOk()
        ->assertSee('Task Demo');
});

it('non-anggota tidak bisa melihat task', function () {
    [$owner, $project, $task] = makeTaskOwner();
    $outsider = User::factory()->create();

    $this->actingAs($outsider)->get(route('tasks.show', $task))->assertForbidden();

    $this->actingAs($outsider)->get(route('tasks.index'))
        ->assertOk()
        ->assertDontSee('Task Demo');
});

it('anggota bisa ubah status tapi tidak bisa edit, hapus, atau kelola anggota', function () {
    [$owner, $project, $task] = makeTaskOwner();
    $member = User::factory()->create();
    $task->assignees()->attach($member->id);

    $this->actingAs($member)
        ->patch(route('tasks.status', $task), ['status' => 'done'])
        ->assertRedirect();
    expect($task->fresh()->status)->toBe('done');

    $this->actingAs($member)->get(route('tasks.edit', $task))->assertForbidden();

    $this->actingAs($member)->delete(route('tasks.destroy', $task))->assertForbidden();
    expect(Task::find($task->id))->not->toBeNull();

    $other = User::factory()->create();
    $this->actingAs($member)
        ->post(route('tasks.members.add', $task), ['user_id' => $other->id])
        ->assertForbidden();
});

it('owner bisa menghapus anggota tapi tidak bisa menghapus dirinya sendiri', function () {
    [$owner, $project, $task] = makeTaskOwner();
    $member = User::factory()->create();
    $task->assignees()->attach($member->id);

    $this->actingAs($owner)
        ->delete(route('tasks.members.remove', [$task, $member]))
        ->assertRedirect();
    expect($task->fresh()->assignees->pluck('id')->all())->not->toContain($member->id);

    $this->actingAs($owner)
        ->delete(route('tasks.members.remove', [$task, $owner]))
        ->assertRedirect();
    expect($task->fresh()->assignees->pluck('id')->all())->toContain($owner->id);
});

it('owner menghapus task maka hilang dari semua anggota', function () {
    [$owner, $project, $task] = makeTaskOwner();
    $member = User::factory()->create();
    $task->assignees()->attach($member->id);
    $taskId = $task->id;

    $this->actingAs($owner)->delete(route('tasks.destroy', $task))->assertRedirect(route('tasks.index'));

    expect(Task::find($taskId))->toBeNull();
    $this->assertDatabaseMissing('task_user', ['task_id' => $taskId]);

    $this->actingAs($member)->get(route('tasks.index'))
        ->assertOk()
        ->assertDontSee('Task Demo');
});
