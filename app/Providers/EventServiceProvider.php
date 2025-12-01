<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\LectureCreated::class => [
            \App\Listeners\SendNotificationListener::class,
        ],
        \App\Events\SectionCreated::class => [
            \App\Listeners\SendNotificationListener::class,
        ],
        \App\Events\TaskCreated::class => [
            \App\Listeners\SendNotificationListener::class,
        ],
        \App\Events\QuizCreated::class => [
            \App\Listeners\SendNotificationListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
