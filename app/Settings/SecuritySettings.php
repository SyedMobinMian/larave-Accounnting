<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SecuritySettings extends Settings
{
    // Password Policy
    public int $password_min_length;
    public bool $password_require_special;
    public bool $password_require_numbers;
    public bool $password_require_mixed_case;
    public int $password_expiry_days;

    // 2FA
    public bool $enable_2fa;
    public bool $enforce_2fa_for_all;

    // Session
    public int $session_lifetime;
    public bool $single_session_per_user;

    // Audit
    public bool $audit_log_enabled;
    public int $audit_log_retention_days;

    // Account Lockout
    public int $max_login_attempts;
    public int $lockout_duration_minutes;

    public static function group(): string
    {
        return 'security';
    }
}

