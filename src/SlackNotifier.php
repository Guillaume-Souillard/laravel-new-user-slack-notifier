<?php

namespace YourNamespace\SlackNotifier;

use Illuminate\Support\Facades\Http;

class SlackNotifier
{
    public static function notify($email)
    {
        $webhookUrl = config('slack-new-user-notifier.webhook_url');
        $appName = config('slack-new-user-notifier.app_name', config('app.name'));
        $maskedEmail = self::maskEmail($email);

        $message = "{$appName} - New user registration: {$maskedEmail}";

        Http::post($webhookUrl, [
            'text' => $message
        ]);
    }

    protected static function maskEmail($email)
    {
        $parts = explode('@', $email);
        return substr($parts[0], 0, 3) . str_repeat('*', strlen($parts[0]) - 3) . '@' . $parts[1];
    }
}
