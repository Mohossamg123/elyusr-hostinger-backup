<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewUserNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $userId,
        public string $userName
    ) {
    }

    /**
     * قنوات إرسال الإشعار
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * بيانات الإشعار التي سيتم تخزينها في notifications
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_user',
            'title' => 'مستخدم جديد',
            'message' => "تم تسجيل مستخدم جديد: {$this->userName}",
            'user_id' => $this->userId,
        ];
    }
}