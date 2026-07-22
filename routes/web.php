<?php

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invoices/{invoice}/pdf', function (Invoice $invoice) {
    $invoice->load(['client', 'items']);
    $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

    return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
})->name('invoices.pdf')->middleware('auth');

use App\Http\Controllers\DocumentPdfController;

Route::middleware(['auth'])->group(function () {
    Route::get('/invoices/{invoice}/pdf', [DocumentPdfController::class, 'downloadInvoice'])->name('invoices.pdf');
    Route::get('/estimates/{estimate}/pdf', [DocumentPdfController::class, 'downloadEstimate'])->name('estimates.pdf');
});