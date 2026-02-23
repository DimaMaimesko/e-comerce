<?php

namespace App\Services\Notification;

class SmsNotification implements NotificationInterface
{
    public function send(string $recipient, string $subject, string $message): bool
    {
        echo "[SMS] To: {$recipient}\n";
        echo "[SMS] {$message}\n\n";
        return true;
    }

    public function getName(): string
    {
        return 'SMS';
    }
}
