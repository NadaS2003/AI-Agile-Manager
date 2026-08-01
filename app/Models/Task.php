<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'user_id',
        'project_id',
        'sprint_id',
        'priority',
        'story_points',
        'sort_order',
        'due_soon_notified_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'due_soon_notified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low'      => 'Low',
            'medium'   => 'Medium',
            'high'     => 'High',
            'critical' => 'Critical',
            default    => 'Medium',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low'      => 'bg-gray-100 text-gray-600',
            'medium'   => 'bg-blue-100 text-blue-700',
            'high'     => 'bg-orange-100 text-orange-700',
            'critical' => 'bg-red-100 text-red-700',
            default    => 'bg-blue-100 text-blue-700',
        };
    }

    public function getPriorityIconAttribute(): string
    {
        return match($this->priority) {
            'low'      => 'arrow_downward',
            'medium'   => 'remove',
            'high'     => 'arrow_upward',
            'critical' => 'priority_high',
            default    => 'remove',
        };
    }
}
