<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\Notification;

class TaskDueSoonNotification extends Notification
{
    public function __construct(public Task $task)
    {
    }

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'task_status' => $this->task->status,
            'task_priority' => $this->task->priority,
            'project_id' => $this->task->project_id,
            'sprint_id' => $this->task->sprint_id,
            'due_date' => $this->task->due_date?->toIso8601String(),
            'message' => 'Alert: Only 24 hours remaining until the final deadline to submit the task',
        ];
    }

    public function toArray(User $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
