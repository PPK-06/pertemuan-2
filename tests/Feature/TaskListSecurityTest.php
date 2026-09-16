<?php

namespace Tests\Feature;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TaskListSecurityTest
 *
 * Cakupan:
 *  - NFR-02: Otorisasi (owner vs non-owner vs guest) pada show & destroy
 *  - NFR-03: Keamanan injeksi (SQL injection & XSS) pada store
 *
 * Asumsi: Model TaskList sudah tersedia dengan kolom
 *         id, owner_id, name, description, timestamps.
 */
class TaskListSecurityTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Buat user baru via forceCreate agar tanpa factory eksternal. */
    private function makeUser(array $attrs = []): User
    {
        return User::forceCreate(array_merge([
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ], $attrs));
    }

    /** Buat TaskList milik $owner via forceCreate. */
    private function makeTaskList(User $owner, array $attrs = []): TaskList
    {
        return TaskList::forceCreate(array_merge([
            'owner_id'    => $owner->id,
            'name'        => 'Dummy List',
            'description' => null,
        ], $attrs));
    }

    // =========================================================================
    // NFR-02 — GET /task-lists/{taskList}  (show)
    // =========================================================================

    /** @test */
    public function owner_can_view_their_task_list(): void
    {
        $owner    = $this->makeUser();
        $taskList = $this->makeTaskList($owner);

        $this->actingAs($owner)
             ->get(route('task-lists.show', $taskList))
             ->assertStatus(200);
    }

    /** @test */
    public function non_owner_is_forbidden_from_viewing_task_list(): void
    {
        $owner    = $this->makeUser();
        $other    = $this->makeUser();
        $taskList = $this->makeTaskList($owner);

        $this->actingAs($other)
             ->get(route('task-lists.show', $taskList))
             ->assertStatus(403);
    }

    /** @test */
    public function guest_is_redirected_when_viewing_task_list(): void
    {
        $owner    = $this->makeUser();
        $taskList = $this->makeTaskList($owner);

        $this->get(route('task-lists.show', $taskList))
             ->assertStatus(302)   // redirect ke /login
             ->assertRedirect(route('login'));
    }

    // =========================================================================
    // NFR-02 — DELETE /task-lists/{taskList}  (destroy)
    // =========================================================================

    /** @test */
    public function owner_can_delete_their_task_list(): void
    {
        $owner    = $this->makeUser();
        $taskList = $this->makeTaskList($owner);

        $this->actingAs($owner)
             ->delete(route('task-lists.destroy', $taskList))
             ->assertStatus(302);   // redirect setelah sukses hapus

        $this->assertDatabaseMissing('task_lists', ['id' => $taskList->id]);
    }

    /** @test */
    public function non_owner_is_forbidden_from_deleting_task_list(): void
    {
        $owner    = $this->makeUser();
        $other    = $this->makeUser();
        $taskList = $this->makeTaskList($owner);

        $this->actingAs($other)
             ->delete(route('task-lists.destroy', $taskList))
             ->assertStatus(403);

        $this->assertDatabaseHas('task_lists', ['id' => $taskList->id]);
    }

    /** @test */
    public function guest_is_redirected_when_deleting_task_list(): void
    {
        $owner    = $this->makeUser();
        $taskList = $this->makeTaskList($owner);

        $this->delete(route('task-lists.destroy', $taskList))
             ->assertStatus(302)
             ->assertRedirect(route('login'));
    }

    // =========================================================================
    // NFR-02 — GET /task-lists  (index)
    // =========================================================================

    /** @test */
    public function authenticated_user_can_access_index(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
             ->get(route('task-lists.index'))
             ->assertStatus(200);
    }

    /** @test */
    public function guest_is_redirected_from_index(): void
    {
        $this->get(route('task-lists.index'))
             ->assertStatus(302)
             ->assertRedirect(route('login'));
    }

    // =========================================================================
    // NFR-03 — POST /task-lists  (store) — SQL Injection & XSS payloads
    // =========================================================================

    /**
     * Payload SQL Injection: ' OR '1'='1
     * Harus lolos validasi (tidak ada error fatal) dan tersimpan aman
     * sebagai string literal — bukan dieksekusi sebagai SQL.
     *
     * @test
     */
    public function sql_injection_in_name_is_stored_safely(): void
    {
        $user    = $this->makeUser();
        $payload = "' OR '1'='1";

        $this->actingAs($user)
             ->post(route('task-lists.store'), ['name' => $payload])
             ->assertRedirect();   // 302 = sukses simpan

        $this->assertDatabaseHas('task_lists', [
            'owner_id' => $user->id,
            'name'     => $payload,   // tersimpan verbatim, bukan hasil injeksi
        ]);
    }

    /**
     * Payload SQL Injection: ; DROP TABLE task_lists;--
     *
     * @test
     */
    public function sql_drop_injection_in_name_is_stored_safely(): void
    {
        $user    = $this->makeUser();
        $payload = '; DROP TABLE task_lists;--';

        $this->actingAs($user)
             ->post(route('task-lists.store'), ['name' => $payload])
             ->assertRedirect();

        // Tabel masih ada → DROP tidak benar-benar dieksekusi
        $this->assertDatabaseHas('task_lists', [
            'owner_id' => $user->id,
            'name'     => $payload,
        ]);
    }

    /**
     * Payload XSS: <script>alert(1)</script>
     * Input diterima dan disimpan mentah di DB (sanitasi dilakukan di view),
     * namun tidak boleh menyebabkan eksekusi server-side yang membahayakan.
     *
     * @test
     */
    public function xss_payload_in_name_is_stored_safely(): void
    {
        $user    = $this->makeUser();
        $payload = '<script>alert(1)</script>';

        $this->actingAs($user)
             ->post(route('task-lists.store'), ['name' => $payload])
             ->assertRedirect();

        $this->assertDatabaseHas('task_lists', [
            'owner_id' => $user->id,
            'name'     => $payload,   // disimpan verbatim; escaping ada di Blade
        ]);
    }

    /**
     * Payload SQL Injection pada field description.
     *
     * @test
     */
    public function sql_injection_in_description_is_stored_safely(): void
    {
        $user    = $this->makeUser();
        $payload = "' OR '1'='1";

        $this->actingAs($user)
             ->post(route('task-lists.store'), [
                 'name'        => 'Safe Name',
                 'description' => $payload,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('task_lists', [
            'owner_id'    => $user->id,
            'description' => $payload,
        ]);
    }

    /**
     * Payload XSS pada field description.
     *
     * @test
     */
    public function xss_payload_in_description_is_stored_safely(): void
    {
        $user    = $this->makeUser();
        $payload = '<script>alert(1)</script>';

        $this->actingAs($user)
             ->post(route('task-lists.store'), [
                 'name'        => 'Safe Name',
                 'description' => $payload,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('task_lists', [
            'owner_id'    => $user->id,
            'description' => $payload,
        ]);
    }

    // =========================================================================
    // NFR-03 — Grep guard: pastikan tidak ada raw query di codebase fitur
    // =========================================================================

    /**
     * Memastikan tidak ada penggunaan whereRaw / DB::raw / selectRaw
     * di dalam file-file Controller/Request yang berkaitan dengan task-lists.
     *
     * @test
     */
    public function feature_files_contain_no_raw_query_calls(): void
    {
        $dangerousPatterns = ['whereRaw', 'DB::raw', 'selectRaw'];

        $filesToScan = [
            app_path('Http/Controllers/TaskListController.php'),
            app_path('Http/Requests/StoreTaskListRequest.php'),
            app_path('Http/Requests/UpdateTaskListRequest.php'),
            app_path('Policies/TaskListPolicy.php'),
        ];

        foreach ($filesToScan as $path) {
            if (! file_exists($path)) {
                // File belum dibuat oleh anggota tim lain — skip.
                continue;
            }

            $contents = file_get_contents($path);

            foreach ($dangerousPatterns as $pattern) {
                $this->assertStringNotContainsString(
                    $pattern,
                    $contents,
                    "Raw query '{$pattern}' ditemukan di {$path}. Harus menggunakan Eloquent / parameter binding."
                );
            }
        }
    }
}
