<?php

namespace App\Providers;

use App\Models\CheckinList;
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
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Stripe\Stripe;

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
            Stripe::setApiKey(config('services.stripe.secret'));
        }
    }

    protected function configureRateLimiting(): void
    {
        // Check-in list password: 5 attempts/min per IP and list, 20/min per list
        // overall (the global cap still holds if client IPs are spoofed).
        RateLimiter::for('checkin-auth', function (Request $request) {
            $token = (string) CheckinList::where('public_url_token', (string) $request->route('token'))
                ->firstOrFail()
                ->public_url_token;
            $tooMany = fn () => back()->withErrors([
                'password' => 'Trop de tentatives. Réessayez dans une minute.',
            ]);

            return [
                Limit::perMinute(5)->by('checkin-auth:'.$token.'|'.$request->ip())->response($tooMany),
                Limit::perMinute(20)->by('checkin-auth:'.$token)->response($tooMany),
            ];
        });

        // Creating checkouts holds stock for 15 minutes: cap it per IP.
        RateLimiter::for('checkout-create', fn (Request $request) => Limit::perMinute(10)
            ->by('checkout-create:'.$request->ip())
            ->response(fn () => back()->with('error', 'Trop de tentatives. Réessayez dans une minute.')));

        // Each request sends an email: cap per checkout, per recipient and per IP.
        RateLimiter::for('checkout-verification-email', function (Request $request) {
            $checkout = $request->route('checkout');
            $uuid = (string) (is_object($checkout) ? $checkout->uuid : $checkout);
            $email = Str::lower((string) $request->input('email'));
            $tooMany = fn () => back()->with('error', 'Trop d\'emails envoyés. Patientez quelques minutes avant de redemander un code.');

            return [
                Limit::perMinutes(10, 3)->by('checkout-verification-email:checkout:'.$uuid)->response($tooMany),
                Limit::perHour(5)->by('checkout-verification-email:email:'.$email)->response($tooMany),
                Limit::perHour(20)->by('checkout-verification-email:ip:'.$request->ip())->response($tooMany),
            ];
        });

        // Codes are also capped per checkout (Checkout::VERIFICATION_MAX_ATTEMPTS).
        RateLimiter::for('checkout-verify', fn (Request $request) => Limit::perMinute(10)
            ->by('checkout-verify:'.$request->ip())
            ->response(fn () => back()->withErrors(['code' => 'Trop de tentatives. Réessayez dans une minute.'])));

        // Each request may send emails with tickets attached.
        RateLimiter::for('lost-tickets', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $tooMany = fn () => back()->withErrors(['email' => 'Trop de demandes. Réessayez plus tard.']);

            return [
                Limit::perHour(3)->by('lost-tickets:email:'.$email)->response($tooMany),
                Limit::perHour(10)->by('lost-tickets:ip:'.$request->ip())->response($tooMany),
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
            fn (): ?Password => app()->isProduction()
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
