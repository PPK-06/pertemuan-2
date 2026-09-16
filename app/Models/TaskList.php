<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaskList extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi lewat mass assignment.
     * 'owner_id' SENGAJA tidak ada di sini supaya tidak bisa dititipkan
     * lewat request body. Nilainya di-set manual di controller dari auth()->id().
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Pemilik daftar tugas ini.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Anggota daftar tugas ini (lewat pivot task_list_user).
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
