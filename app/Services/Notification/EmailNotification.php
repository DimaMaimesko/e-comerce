<?php

namespace App\Services\Notification;

class EmailNotification implements NotificationInterface
{
    public function send(string $recipient, string $subject, string $message): bool
    {
        echo "[EMAIL] To: {$recipient}\n";
        echo "[EMAIL] Subject: {$subject}\n";
        echo "[EMAIL] Message: {$message}\n\n";
        return true;
    }

    public function getName(): string
    {
        return 'Email';
    }
}
