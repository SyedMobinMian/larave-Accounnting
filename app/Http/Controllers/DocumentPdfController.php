<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentPdfController extends Controller
{
    public function downloadInvoice(Invoice $invoice)
    {
        $invoice->load(['client', 'items']);

        $data = [
            'type' => 'INVOICE',
            'title' => __('Invoice'),
            'number' => $invoice->invoice_number,
            'date' => $invoice->date,
            'due_date' => $invoice->due_date ?? null,
            'status' => $invoice->status,
            'client' => $invoice->client,
            'items' => $invoice->items ?? [],
            'subtotal' => $invoice->subtotal ?? $invoice->total,
            'tax_total' => $invoice->tax_total ?? 0,
            'total' => $invoice->total,
            'notes' => $invoice->notes ?? '',
            'terms' => $invoice->terms ?? __('Payment due within 15 days from date of invoice.'),
        ];

        $pdf = Pdf::loadView('pdf.document', $data)->setPaper('a4', 'portrait');

        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function downloadEstimate(Estimate $estimate)
    {
        $estimate->load(['client', 'items']);

        $data = [
            'type' => 'ESTIMATE',
            'title' => __('Estimate / Quotation'),
            'number' => $estimate->estimate_number,
            'date' => $estimate->estimate_date,
            'due_date' => $estimate->expiry_date ?? null,
            'status' => $estimate->status,
            'client' => $estimate->client,
            'items' => $estimate->items ?? [],
            'subtotal' => $estimate->subtotal ?? $estimate->total,
            'tax_total' => $estimate->tax_total ?? 0,
            'total' => $estimate->total,
            'notes' => $estimate->notes ?? '',
            'terms' => $estimate->terms ?? __('Valid for 30 days from issue date.'),
        ];

        $pdf = Pdf::loadView('pdf.document', $data)->setPaper('a4', 'portrait');

        return $pdf->download($estimate->estimate_number . '.pdf');
    }
}