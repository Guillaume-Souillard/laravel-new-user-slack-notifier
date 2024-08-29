# Laravel New User Slack Notifier

A Laravel package to send Slack notifications when a new user registers on your application.

## Installation

You can install the package via Composer:

```
composer require gsouillard/laravel-new-user-slack-notifier
```

## Configuration

After installing the package, publish the configuration file:

```
php artisan vendor:publish --tag=config
```

This will create a configuration file named `slack-new-user-notifier.php` in your `config` directory.

### Configuration Options

- **webhook_url**: The Slack Webhook URL where the notifications will be sent. This should be set in your `.env` file as `SLACK_NEW_USER_NOTIFIER_WEBHOOK_URL`.
- **app_name**: The name of your application, which will be included in the Slack notification. It defaults to the `APP_NAME` defined in your `.env` file, but you can override it in the configuration file.
- **event_to_listen**: Determines which event to listen to. You can set this to `Registered` (default) or `Verified` to send notifications based on the specific event.

Example configuration:

```php
return [
    'webhook_url' => env('SLACK_NEW_USER_NOTIFIER_WEBHOOK_URL', ''),
    'app_name' => env('APP_NAME', 'Your Application'), // Default: APP_NAME
    'event_to_listen' => env('SLACK_NEW_USER_NOTIFIER_EVENT', 'Registered'), // Default: Registered
];
```

## Usage

This package works by listening to either the `Registered` or `Verified` events in Laravel. By default, it listens to the `Registered` event. You can change this by setting the `event_to_listen` configuration option.

In your `EventServiceProvider`, the package automatically registers the listener based on your configuration:

```php
use YourNamespace\SlackNotifier\SlackNotifier;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();

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
```

### Example .env Configuration

In your `.env` file, add:

```
SLACK_NEW_USER_NOTIFIER_WEBHOOK_URL=https://hooks.slack.com/services/XXXXX/XXXXX/XXXXX
APP_NAME=My Awesome App
SLACK_NEW_USER_NOTIFIER_EVENT=Registered
```

Set `SLACK_NEW_USER_NOTIFIER_EVENT` to `Verified` if you want to send notifications only after the user verifies their email.

## Testing

To run the tests, you can use PHPUnit:

```
vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
