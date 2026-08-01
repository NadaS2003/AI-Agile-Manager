<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'color',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class)->latest();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function backlogTasks(): HasMany
    {
        return $this->hasMany(Task::class)->whereNull('sprint_id');
    }

    public function activeSprint()
    {
        return $this->sprints()->where('status', 'active')->first();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'planning'  => 'Planning',
            'active'    => 'Active',
            'on_hold'   => 'On Hold',
            'completed' => 'Completed',
            'archived'  => 'Archived',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planning'  => 'bg-blue-100 text-blue-700',
            'active'    => 'bg-green-100 text-green-700',
            'on_hold'   => 'bg-yellow-100 text-yellow-700',
            'completed' => 'bg-purple-100 text-purple-700',
            'archived'  => 'bg-gray-100 text-gray-500',
            default     => 'bg-gray-100 text-gray-500',
        };
    }
}
