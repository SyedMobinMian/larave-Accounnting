<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    // Default Gateway
    public string $default_payment_gateway;

    // Stripe
    public ?string $stripe_key;
    public ?string $stripe_secret;
    public ?string $stripe_webhook_secret;

    // PayPal
    public ?string $paypal_client_id;
    public ?string $paypal_secret;
    public bool $paypal_sandbox_mode;

    // Razorpay
    public ?string $razorpay_key;
    public ?string $razorpay_secret;

    // Cash on Delivery
    public bool $enable_cod;
    public float $cod_max_amount;

    // Bank Transfer
    public bool $enable_bank_transfer;
    public ?string $bank_instructions;

    // UPI
    public bool $enable_upi;
    public ?string $upi_id;

    public static function group(): string
    {
        return 'payment';
    }
}

