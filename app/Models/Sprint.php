<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sprint extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'goal',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('sort_order');
    }

    public function getTotalStoryPointsAttribute(): int
    {
        return $this->tasks->sum('story_points') ?? 0;
    }

    public function getCompletedStoryPointsAttribute(): int
    {
        return $this->tasks->where('status', 'completed')->sum('story_points') ?? 0;
    }

    public function getProgressPercentAttribute(): int
    {
        $total = $this->totalStoryPoints;
        if ($total === 0) return 0;
        return (int) round(($this->completedStoryPoints / $total) * 100);
    }

    public function getDaysLeftAttribute(): int
    {
        if (!$this->end_date) return 0;
        return max(0, now()->startOfDay()->diffInDays($this->end_date->startOfDay(), false));
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'planning'  => 'Planning',
            'active'    => 'Active',
            'completed' => 'Completed',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planning'  => 'bg-blue-100 text-blue-700',
            'active'    => 'bg-green-100 text-green-700',
            'completed' => 'bg-purple-100 text-purple-700',
            default     => 'bg-gray-100 text-gray-500',
        };
    }
}
