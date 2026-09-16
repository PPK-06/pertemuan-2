<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    /**
     * Alias boolean untuk UI: true jika status done.
     * Memungkinkan Blade memakai $task->completed walau kolom DB-nya status.
     */
    public function getCompletedAttribute(): bool
    {
        return $this->status === 'done';
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class);
    }
}
