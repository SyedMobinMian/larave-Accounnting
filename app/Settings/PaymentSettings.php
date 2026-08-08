<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    // Default Gateway
    public string $default_payment_gateway = 'bank_transfer';

    // Stripe
    public ?string $stripe_key = null;
    public ?string $stripe_secret = null;
    public ?string $stripe_webhook_secret = null;

    // PayPal
    public ?string $paypal_client_id = null;
    public ?string $paypal_secret = null;
    public bool $paypal_sandbox_mode = true;

    // Razorpay
    public ?string $razorpay_key = null;
    public ?string $razorpay_secret = null;

    // Cash on Delivery
    public bool $enable_cod = false;
    public float $cod_max_amount = 50000;

    // Bank Transfer
    public bool $enable_bank_transfer = true;
    public ?string $bank_instructions = null;

    // UPI
    public bool $enable_upi = true;
    public ?string $upi_id = null;

    public static function group(): string
    {
        return 'payment';
    }
}

