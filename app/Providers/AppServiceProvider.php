<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Filament\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        Notifications::alignment(Alignment::Center);
        Notifications::verticalAlignment(VerticalAlignment::Start);

        if (config('services.stripe.secret')) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        }
    }

    protected function configureRateLimiting(): void
    {
        // Check-in list password: 5 attempts/min per IP and list, 20/min per list
        // overall (the global cap still holds if client IPs are spoofed).
        RateLimiter::for('checkin-auth', function (Request $request) {
            $token = (string) $request->route('token');
            $tooMany = fn () => back()->withErrors([
                'password' => 'Trop de tentatives. Réessayez dans une minute.',
            ]);

            return [
                Limit::perMinute(5)->by('checkin-auth:'.$token.'|'.$request->ip())->response($tooMany),
                Limit::perMinute(20)->by('checkin-auth:'.$token)->response($tooMany),
            ];
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null,
        );
    }
}
