<?php

return [
    'webhook_url' => env('SLACK_NEW_USER_NOTIFIER_WEBHOOK_URL', ''),
    'app_name' => env('APP_NAME', 'Your Application'), // Default: APP_NAME
    'event_to_listen' => env('SLACK_NEW_USER_NOTIFIER_EVENT', 'Registered'), // Default: Registered
];
