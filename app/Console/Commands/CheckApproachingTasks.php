<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckApproachingTasks extends Command
{
    protected $signature = 'tasks:check-approaching';

    protected $description = 'Send a database notification when a task is 24 hours away from its due date.';

    public function handle(): int
    {
        $windowStart = now()->addDay();
        $windowEnd = now()->addDay()->addHour();

        Task::query()
            ->whereNotNull('due_date')
            ->whereNull('due_soon_notified_at')
            ->whereBetween('due_date', [$windowStart, $windowEnd])
            ->with('user')
            ->chunkById(100, function ($tasks): void {
                foreach ($tasks as $task) {
                    if ($task->user === null) {
                        continue;
                    }

                    Notification::send($task->user, new TaskDueSoonNotification($task));

                    $task->forceFill([
                        'due_soon_notified_at' => now(),
                    ])->save();
                }
            });

        $this->info('Approaching task notifications checked successfully.');

        return self::SUCCESS;
    }
}
