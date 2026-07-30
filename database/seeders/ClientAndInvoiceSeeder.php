<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientAndInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fetch reference products
        $laptop = DB::table('products')->where('sku', 'PROD-DELL-001')->first();
        $service = DB::table('products')->where('sku', 'SERV-WEB-001')->first();

        $firstProduct = $laptop ?? DB::table('products')->first();
        $secondProduct = $service ?? DB::table('products')->skip(1)->first() ?? $firstProduct;

        // 2. Seed Clients
        $clients = [
            [
                'company'          => 'Acme Corporation',
                'vat'              => 'US987654321',
                'phonenumber'      => '+1 (555) 019-2834',
                'country'          => 'USA',
                'city'             => 'Austin',
                'zip'              => '78701',
                'state'            => 'TX',
                'address'          => '123 Innovation Way, Tech Park',
                'website'          => 'https://acme.corp',
                'active'           => true,
                'billing_street'   => '123 Innovation Way',
                'billing_city'     => 'Austin',
                'billing_state'    => 'TX',
                'billing_zip'      => '78701',
                'billing_country'  => 'USA',
                'shipping_street'  => '123 Innovation Way',
                'shipping_city'    => 'Austin',
                'shipping_state'   => 'TX',
                'shipping_zip'     => '78701',
                'shipping_country' => 'USA',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'company'          => 'Nexus Global Solutions',
                'vat'              => 'GB123456789',
                'phonenumber'      => '+44 20 7946 0912',
                'country'          => 'UK',
                'city'             => 'London',
                'zip'              => 'EC2N 2DB',
                'state'            => 'Greater London',
                'address'          => '45 Financial Center St',
                'website'          => 'https://nexusglobal.io',
                'active'           => true,
                'billing_street'   => '45 Financial Center St',
                'billing_city'     => 'London',
                'billing_state'    => 'Greater London',
                'billing_zip'      => 'EC2N 2DB',
                'billing_country'  => 'UK',
                'shipping_street'  => '45 Financial Center St',
                'shipping_city'    => 'London',
                'shipping_state'   => 'Greater London',
                'shipping_zip'     => 'EC2N 2DB',
                'shipping_country' => 'UK',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];

        foreach ($clients as $clientData) {
            $clientId = DB::table('clients')->insertGetId($clientData);

            // 3. Seed Contact (Exactly matching your contacts table migration)
            DB::table('contacts')->insert([
                'client_id'   => $clientId,
                'is_primary'  => true,
                'firstname'   => 'John',
                'lastname'    => 'Doe',
                'email'       => 'john.doe@' . Str::slug($clientData['company']) . '.com',
                'phonenumber' => $clientData['phonenumber'],
                'title'       => 'Account Manager',
                'active'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // 4. Seed Invoices
            $isPaid = ($clientId % 2 === 1);
            $subtotal = 0;

            $invoiceId = DB::table('invoices')->insertGetId([
                'client_id'      => $clientId,
                'invoice_number' => 'INV-2026-' . str_pad($clientId, 4, '0', STR_PAD_LEFT),
                'issue_date'     => now()->subDays(10),
                'due_date'       => now()->addDays(20),
                'status'         => $isPaid ? 'paid' : 'sent',
                'subtotal'       => 0,
                'tax_amount'     => 0,
                'total_amount'   => 0,
                'notes'          => 'Thank you for your business!',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // 5. Line Items
            if ($firstProduct) {
                $qty1 = 2;
                $price1 = $firstProduct->sales_price ?? 1499.99;
                $lineTotal1 = $qty1 * $price1;
                $subtotal += $lineTotal1;

                DB::table('invoice_items')->insert([
                    'invoice_id'  => $invoiceId,
                    'product_id'  => $firstProduct->id,
                    'description' => $firstProduct->name,
                    'quantity'    => $qty1,
                    'unit_price'  => $price1,
                    'total_price' => $lineTotal1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            if ($secondProduct) {
                $qty2 = 10;
                $price2 = $secondProduct->sales_price ?? 85.00;
                $lineTotal2 = $qty2 * $price2;
                $subtotal += $lineTotal2;

                DB::table('invoice_items')->insert([
                    'invoice_id'  => $invoiceId,
                    'product_id'  => $secondProduct->id,
                    'description' => $secondProduct->name,
                    'quantity'    => $qty2,
                    'unit_price'  => $price2,
                    'total_price' => $lineTotal2,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            // Update Totals
            $taxAmount = $subtotal * 0.10;
            $totalAmount = $subtotal + $taxAmount;

            DB::table('invoices')->where('id', $invoiceId)->update([
                'subtotal'     => $subtotal,
                'tax_amount'   => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            // 6. Payment
            if ($isPaid) {
                DB::table('invoice_payments')->insert([
                    'invoice_id'     => $invoiceId,
                    'payment_number' => 'PAY-2026-' . str_pad($clientId, 4, '0', STR_PAD_LEFT),
                    'payment_date'   => now()->subDays(2),
                    'amount'         => $totalAmount,
                    'payment_method' => 'bank_transfer',
                    'reference'      => 'TXN-' . Str::random(8),
                    'notes'          => 'Payment received in full via Wire Transfer.',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }
}
