<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>INVOICE</h2>
        <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>Date:</strong> {{ $invoice->date }}</p>
        <p><strong>Client:</strong> {{ $invoice->client->company ?? $invoice->client->primary_contact }}</p>
        <p><strong>Status:</strong> {{ strtoupper($invoice->status) }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">₹{{ number_format($item->rate, 2) }}</td>
                <td class="text-right">₹{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;" class="text-right">
        <p><strong>Subtotal:</strong> ₹{{ number_format($invoice->subtotal, 2) }}</p>
        <p><strong>Tax:</strong> ₹{{ number_format($invoice->total_tax, 2) }}</p>
        <h3><strong>Total:</strong> ₹{{ number_format($invoice->total, 2) }}</h3>
    </div>
</body>
</html>