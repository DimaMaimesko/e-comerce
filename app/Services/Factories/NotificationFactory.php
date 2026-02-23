<?php

namespace App\Services\Factories;

use App\Services\Notification\NotificationInterface;
use App\Services\Notification\EmailNotification;
use App\Services\Notification\SmsNotification;
use App\Services\Notification\PushNotification;

class NotificationFactory
{
    public function createNotification(string $type): NotificationInterface
    {
        return match(strtolower($type)) {
            'email' => new EmailNotification(),
            'sms' => new SmsNotification(),
            'push' => new PushNotification(),
            default => throw new \InvalidArgumentException(
                "Unsupported notification type: $type"
            )
        };
    }

    public function createAll(): array
    {
        return [
            $this->createNotification('email'),
            $this->createNotification('sms'),
            $this->createNotification('push')
        ];
    }
}
