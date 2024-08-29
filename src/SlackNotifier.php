<?php

namespace YourNamespace\SlackNotifier;

use Illuminate\Support\Facades\Http;
use Exception;

class SlackNotifier
{
    public static function notify($email)
    {
        $webhookUrl = config('slack-new-user-notifier.webhook_url');

        if (empty($webhookUrl)) {
            throw new Exception('Slack webhook URL is not set.');
        }

        $appName = config('app.name') ?: config('slack-new-user-notifier.app_name');

        if (empty($appName)) {
            throw new Exception('Application name is not set.');
        }

        $maskedEmail = self::maskEmail($email);
        $message = "{$appName} - New user registration: {$maskedEmail}";

        Http::post($webhookUrl, [
            'text' => $message
        ]);
    }

    public static function maskEmail($email)
    {
        $parts = explode('@', $email);
        return substr($parts[0], 0, 2) . '***@' . $parts[1];
    }
}
