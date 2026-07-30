<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Filament\Notifications\Notification;

class RazorpayPaymentController extends Controller
{
    public function handleSuccess(Request $request)
    {
        $razorpayPaymentId = $request->input('razorpay_payment_id');
        $razorpayOrderId = $request->input('razorpay_order_id');
        $razorpaySignature = $request->input('razorpay_signature');
        $invoiceId = $request->input('invoice_id');

        $invoice = Invoice::findOrFail($invoiceId);

        $api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));

        // Verify Razorpay Signature
        try {
            $attributes = [
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Record Payment Entry in Accounting Ledger
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total,
                'payment_date' => now(),
                'payment_method' => 'Razorpay (Online)',
                'reference_number' => $razorpayPaymentId,
                'notes' => 'Paid via Client Portal Test Checkout',
            ]);

            // Update Invoice Status
            $invoice->update([
                'status' => 'paid',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment successful and recorded!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ], 400);
        }
    }
}