<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SecuritySettings extends Settings
{
    // Password Policy
    public int $password_min_length = 8;
    public bool $password_require_special = true;
    public bool $password_require_numbers = true;
    public bool $password_require_mixed_case = true;
    public int $password_expiry_days = 90;

    // 2FA
    public bool $enable_2fa = false;
    public bool $enforce_2fa_for_all = false;

    // Session
    public int $session_lifetime = 120;
    public bool $single_session_per_user = false;

    // Audit
    public bool $audit_log_enabled = true;
    public int $audit_log_retention_days = 365;

    // Account Lockout
    public int $max_login_attempts = 5;
    public int $lockout_duration_minutes = 30;

    public static function group(): string
    {
        return 'security';
    }
}

