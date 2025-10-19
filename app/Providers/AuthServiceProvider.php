<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\ExportOffer;
use App\Models\Listing;
use App\Models\Load;
use App\Models\Order;
use App\Models\Review;
use App\Models\Trip;
use App\Policies\ExportOfferPolicy;
use App\Policies\ListingPolicy;
use App\Policies\LoadPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\TripPolicy;
use App\Models\ExportShipment;
use App\Models\Invoice;
use App\Models\Payout;
use App\Policies\ExportShipmentPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PayoutPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Listing::class => ListingPolicy::class,
        Order::class => OrderPolicy::class,
        Load::class => LoadPolicy::class,
        Trip::class => TripPolicy::class,
        ExportOffer::class => ExportOfferPolicy::class,
        Review::class => ReviewPolicy::class,
        \App\Models\Message::class => \App\Policies\MessagePolicy::class,
        ExportShipment::class => ExportShipmentPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Payout::class => PayoutPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
