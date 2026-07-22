<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\InvoicePayment;
use App\Observers\ExpenseObserver;
use App\Observers\InvoicePaymentObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Expense::observe(ExpenseObserver::class);
        InvoicePayment::observe(InvoicePaymentObserver::class);

        Model::unguard();

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'hi', 'de', 'es'])
                ->labels([
                    'en' => 'English',
                    'hi' => 'हिन्दी',
                    'de' => 'Deutsch',
                    'es' => 'Español',
                ])
                ->flags([
                    'en' => asset('flags/en.gif'),
                    'hi' => asset('flags/hi.gif'),
                    'de' => asset('flags/de.gif'),
                    'es' => asset('flags/es.gif'),
                ])
                ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER) // Explicitly force rendering on topbar
                ->visible(true);
        });
    }
}