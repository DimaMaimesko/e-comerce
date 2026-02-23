<?php

namespace App\Services\Notification;

class PushNotification implements NotificationInterface
{
    public function send(string $recipient, string $subject, string $message): bool
    {
        echo "[PUSH] To: {$recipient}\n";
        echo "[PUSH] Title: {$subject}\n";
        echo "[PUSH] Body: {$message}\n\n";
        return true;
    }

    public function getName(): string
    {
        return 'Push Notification';
    }
}
