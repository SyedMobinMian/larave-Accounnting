<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class EmailSettings extends Settings
{
    // SMTP Settings
    public string $smtp_host = 'smtp.gmail.com';
    public int $smtp_port = 587;
    public ?string $smtp_username = null;
    public ?string $smtp_password = null;
    public string $smtp_encryption = 'tls';

    // Mail From
    public string $mail_from_address = 'noreply@example.com';
    public string $mail_from_name = 'My Company';

    // Notifications
    public bool $enable_email_notifications = true;
    public bool $notify_on_invoice_creation = true;
    public bool $notify_on_payment_received = true;
    public bool $notify_on_low_stock = true;

    // Password / Notification prefs
    public bool $notification_preferences = false;

    public static function group(): string
    {
        return 'email';
    }
}

