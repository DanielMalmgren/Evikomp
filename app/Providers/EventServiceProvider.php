<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Aacotroneo\Saml2\Events\Saml2LoginEvent' => [
            'App\Listeners\LoginListener',
        ],
        'Aacotroneo\Saml2\Events\Saml2LogoutEvent' => [
            'App\Listeners\LogoutListener',
        ],
    ];

}
