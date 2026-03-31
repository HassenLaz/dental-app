<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Treatment;
use App\Models\Payment;
use App\Models\TreatmentPlan;
use App\Policies\TreatmentPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\TreatmentPlanPolicy;

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
        Gate::policy(Treatment::class,     TreatmentPolicy::class);
        Gate::policy(Payment::class,       PaymentPolicy::class);
        Gate::policy(TreatmentPlan::class, TreatmentPlanPolicy::class);
    }
}
