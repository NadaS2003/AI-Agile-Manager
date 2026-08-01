<?php

namespace App\Support;

use App\Models\UserNotification;

class UserNotifier
{
    public static function notify(int $userId, string $type, string $title, ?string $body = null, ?string $url = null): \App\Models\UserNotification
    {
        return \App\Models\UserNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'url' => $url,
        ]);
    }
}
