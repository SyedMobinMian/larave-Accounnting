<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }
        .header-table, .details-table, .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .company-logo {
            max-height: 60px;
        }
        .doc-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            background: #e2e8f0;
            color: #475569;
        }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-unpaid { background: #fef3c7; color: #b45309; }
        .section-margin {
            margin-top: 25px;
        }
        .items-table th {
            background-color: #f8fafc;
            color: #475569;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right {
            text-align: right;
        }
        .totals-table {
            width: 40%;
            float: right;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
        }
        .totals-table .grand-total {
            font-size: 15px;
            font-weight: bold;
            background-color: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }
        .footer-notes {
            margin-top: 50px;
            clear: both;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <h2 style="margin: 0; color: #0f172a;">{{ config('app.name', 'Perfex CRM') }}</h2>
                <p style="margin: 4px 0; color: #64748b;">
                    Business HQ, Commerce Tower<br>
                    GSTIN/TAX ID: 22AAAAA0000A1Z5<br>
                    Email: billing@company.com
                </p>
            </td>
            <td style="width: 50%;" class="text-right">
                <div class="doc-title">{{ $title }}</div>
                <div style="margin-top: 5px;">
                    <strong># {{ $number }}</strong>
                </div>
                <div style="margin-top: 5px;">
                    <span class="badge {{ $status === 'paid' ? 'badge-paid' : 'badge-unpaid' }}">
                        {{ strtoupper($status) }}
                    </span>
                </div>
            </td>
        </tr>
    </table>
    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
    <!-- Billed To & Dates Section -->
    <table class="details-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong style="color: #64748b; text-transform: uppercase; font-size: 10px;">Billed To:</strong><br>
                <strong style="font-size: 14px; color: #1e293b;">{{ $client->name ?? 'N/A' }}</strong><br>
                {{ $client->company_name ?? '' }}<br>
                {{ $client->email ?? '' }}<br>
                {{ $client->phone ?? '' }}
            </td>
            <td style="width: 50%; vertical-align: top;" class="text-right">
                <table style="width: 100%;">
                    <tr>
                        <td class="text-right" style="color: #64748b;">Issue Date:</td>
                        <td class="text-right"><strong>{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</strong></td>
                    </tr>
                    @if($due_date)
                    <tr>
                        <td class="text-right" style="color: #64748b;">Due/Expiry Date:</td>
                        <td class="text-right"><strong>{{ \Carbon\Carbon::parse($due_date)->format('d M, Y') }}</strong></td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
    <!-- Items Table -->
    <div class="section-margin">
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th class="text-right" style="width: 15%;">Qty</th>
                    <th class="text-right" style="width: 15%;">Unit Price</th>
                    <th class="text-right" style="width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">₹{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8;">No items listed</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Totals Table -->
    <table class="totals-table">
        <tr>
            <td style="color: #64748b;">Subtotal:</td>
            <td class="text-right">₹{{ number_format($subtotal, 2) }}</td>
        </tr>
        @if($tax_total > 0)
        <tr>
            <td style="color: #64748b;">Tax Total:</td>
            <td class="text-right">₹{{ number_format($tax_total, 2) }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td>Total:</td>
            <td class="text-right">₹{{ number_format($total, 2) }}</td>
        </tr>
    </table>
    <!-- Footer Notes & Terms -->
    <div class="footer-notes">
        @if($notes)
            <p><strong>Notes:</strong> {{ $notes }}</p>
        @endif
        <p><strong>Terms & Conditions:</strong> {{ $terms }}</p>
        <p style="text-align: center; margin-top: 30px; font-size: 10px; color: #94a3b8;">
            This is a computer-generated document. No signature required.
        </p>
    </div>
</body>
</html>