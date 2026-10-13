<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $userName
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'welcome',
            'title' => 'مرحبًا بك',
            'message' => "أهلًا بك {$this->userName}، تم إنشاء حسابك بنجاح.",
        ];
    }
}