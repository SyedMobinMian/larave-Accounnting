<?php

use App\Http\Controllers\DocumentPdfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
})->name('landing');

// Document PDF downloads
Route::get('/documents/invoices/{invoice}/pdf', [DocumentPdfController::class, 'downloadInvoice'])
    ->name('invoices.pdf');

Route::get('/documents/estimates/{estimate}/pdf', [DocumentPdfController::class, 'downloadEstimate'])
    ->name('estimates.pdf');

