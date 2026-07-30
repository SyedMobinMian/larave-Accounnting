<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class EmailSettings extends Settings
{
    // SMTP Settings
    public string $smtp_host;
    public int $smtp_port;
    public ?string $smtp_username;
    public ?string $smtp_password;
    public string $smtp_encryption;

    // Mail From
    public string $mail_from_address;
    public string $mail_from_name;

    // Notifications
    public bool $enable_email_notifications;
    public bool $notify_on_invoice_creation;
    public bool $notify_on_payment_received;
    public bool $notify_on_low_stock;

    public static function group(): string
    {
        return 'email';
    }
}

