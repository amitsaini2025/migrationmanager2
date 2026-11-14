<?php

namespace App\Providers;

use App\Listeners\AttachSesDeliveryHeaders;
use App\Listeners\MarkZohoMailDelivered;
use App\Listeners\TagZohoTransport;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mailer\Event\MessageEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MessageSending::class => [
            AttachSesDeliveryHeaders::class,
        ],
        MessageEvent::class => [
            TagZohoTransport::class,
        ],
        MessageSent::class => [
            MarkZohoMailDelivered::class,
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
