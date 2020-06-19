<?php

namespace App\Providers;

use App\Events\OrderPaid;
use App\Events\OrderReviewed;
use App\Listeners\UpdateCrowdfundingProductProgress;
use App\Listeners\UpdateReviewRating;
use App\Listeners\UpdateSoldCount;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderPaid::class => [
            UpdateSoldCount::class,
            UpdateCrowdfundingProductProgress::class
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderReviewed::class => [
            UpdateReviewRating::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
