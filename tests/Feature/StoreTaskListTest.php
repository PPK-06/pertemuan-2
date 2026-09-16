<?php

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// RefreshDatabase belum diaktifkan global di tests/Pest.php, jadi dipasang di sini.
uses(RefreshDatabase::class);

it('menyimpan daftar tugas dengan pembuatnya sebagai pemilik', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('task-lists.store'), [
        'name' => 'Belanja Mingguan',
        'description' => 'Daftar belanja untuk seminggu.',
    ]);

    $taskList = TaskList::first();

    expect($taskList)->not->toBeNull()
        ->and($taskList->name)->toBe('Belanja Mingguan')
        ->and($taskList->description)->toBe('Daftar belanja untuk seminggu.')
        ->and($taskList->owner_id)->toBe($user->id);

    $response->assertRedirect(route('task-lists.show', $taskList))
        ->assertSessionHas('success');

    // Ikuti redirect-nya sekalian, supaya show.blade.php benar-benar ter-render.
    $this->actingAs($user)->get(route('task-lists.show', $taskList))
        ->assertOk()
        ->assertSee('Belanja Mingguan');
});

it('merender halaman index dan create untuk user yang login', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('task-lists.index'))
        ->assertOk()
        ->assertSee('Daftar Tugas');

    $this->actingAs($user)->get(route('task-lists.create'))
        ->assertOk()
        ->assertSee('Buat Daftar Tugas');
});

it('mengabaikan owner_id yang dikirim lewat request body', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->post(route('task-lists.store'), [
        'name' => 'Daftar Titipan',
        'owner_id' => $other->id,
    ]);

    // owner_id harus tetap dari sesi login, bukan dari body.
    expect(TaskList::first()->owner_id)->toBe($user->id)
        ->not->toBe($other->id);
});

it('menambahkan pemilik sebagai anggota di tabel pivot', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('task-lists.store'), [
        'name' => 'Persiapan Demo',
    ]);

    $this->assertDatabaseHas('task_list_user', [
        'task_list_id' => TaskList::first()->id,
        'user_id' => $user->id,
    ]);
});

it('menolak guest dan tidak menyimpan apa pun', function () {
    $response = $this->post(route('task-lists.store'), [
        'name' => 'Daftar Tanpa Login',
    ]);

    $response->assertRedirect(route('login'));

    expect(TaskList::count())->toBe(0);
});

it('menolak nama kosong dan tidak menyimpan apa pun', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('task-lists.store'), [
        'name' => '',
        'description' => 'Deskripsi tanpa nama.',
    ]);

    $response->assertSessionHasErrors('name');

    expect(TaskList::count())->toBe(0);
});
