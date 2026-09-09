<?php

// Salin ISI file ini ke migration yang sudah kamu generate:
// database/migrations/xxxx_xx_xx_xxxxxx_add_role_to_users_table.php
// Jangan copy file ini apa adanya, karena nama filenya harus
// tetap memakai timestamp hasil generate artisan milikmu.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Guard: kalau nanti ada anggota tim lain yang juga membuat
            // migration kolom 'role', migrate tidak akan gagal dengan
            // error "duplicate column name: role" saat PM merge.
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
