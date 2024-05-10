<?php

namespace App\Providers;

use App\Events\ActionCreated;
use App\Events\NotificationCreate;
use App\Events\UserCreated;
use App\Listeners\CreateActionUser;
use App\Listeners\CreateGroupUser;
use App\Listeners\MakeUserSlug;
use App\Listeners\NotificationCreateListener;
use App\Listeners\SendActionMail;
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
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserCreated::class => [
            MakeUserSlug::class,
            CreateGroupUser::class,
            CreateActionUser::class,
        ],
        ActionCreated::class => [
            SendActionMail::class,
        ],
        NotificationCreate::class => [
            NotificationCreateListener::class,
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
