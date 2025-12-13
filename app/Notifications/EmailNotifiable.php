<?php

namespace App\Notifications;

use Illuminate\Notifications\Notifiable;

class EmailNotifiable
{
    use Notifiable;

    protected $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function routeNotificationForMail()
    {
        return $this->email;
    }

    /**
     * Provide a stable identifier so Notification fakes can track this notifiable.
     */
    public function getKey(): string
    {
        return (string) $this->email;
    }

    public function getKeyName(): string
    {
        return 'email';
    }
}
