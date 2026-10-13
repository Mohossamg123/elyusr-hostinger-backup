<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDonationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $donationId,
        public string $donorName,
        public string $amount
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_donation',
            'title' => 'تبرع جديد',
            'message' => "تم تسجيل تبرع جديد من {$this->donorName} بقيمة {$this->amount} جنيه",
            'donation_id' => $this->donationId,
        ];
    }
}