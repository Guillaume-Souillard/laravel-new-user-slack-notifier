<?php

namespace YourNamespace\SlackNotifier;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class SlackNotifierServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/slack-new-user-notifier.php', 'slack-new-user-notifier'
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/slack-new-user-notifier.php' => config_path('slack-new-user-notifier.php'),
            ], 'config');
        }

        $eventToListen = config('slack-new-user-notifier.event_to_listen', 'Registered');

        if ($eventToListen === 'Verified') {
            Event::listen(Verified::class, function ($event) {
                SlackNotifier::notify($event->user->email);
            });
        } else {
            Event::listen(Registered::class, function ($event) {
                SlackNotifier::notify($event->user->email);
            });
        }
    }
}
