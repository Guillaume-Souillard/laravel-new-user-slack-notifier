<?php

namespace YourNamespace\SlackNotifier\Tests\Unit;

use Orchestra\Testbench\TestCase;
use YourNamespace\SlackNotifier\SlackNotifier;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class SlackNotifierTest extends TestCase
{
    /** @test */
    public function it_sends_notification_with_masked_email()
    {
        // Set config values
        Config::set('slack-new-user-notifier.webhook_url', 'https://hooks.slack.com/services/test/test/test');
        Config::set('app.name', 'Test App From app.name');

        // Mock HTTP response
        Http::fake();

        $email = 'testuser@example.com';
        SlackNotifier::notify($email);

        $maskedEmail = 'te***@example.com';
        Http::assertSent(function ($request) use ($maskedEmail) {
            return $request['text'] === 'Test App From app.name - New user registration: ' . $maskedEmail;
        });
    }

    /** @test */
    public function it_checks_that_webhook_url_is_set()
    {
        // Set webhook URL to null to trigger the exception
        Config::set('slack-new-user-notifier.webhook_url', null);
        Config::set('app.name', 'Test App From app.name');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Slack webhook URL is not set.');

        SlackNotifier::notify('testuser@example.com');
    }

    /** @test */
    public function it_checks_that_app_name_is_set()
    {
        // Set webhook URL
        Config::set('slack-new-user-notifier.webhook_url', 'https://hooks.slack.com/services/test/test/test');
        // Set app name to null to trigger the exception
        Config::set('app.name', null);
        Config::set('slack-new-user-notifier.app_name', null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Application name is not set.');

        SlackNotifier::notify('testuser@example.com');
    }

    /** @test */
    public function it_uses_slack_notifier_app_name_if_app_name_is_missing()
    {
        // Set config values
        Config::set('slack-new-user-notifier.webhook_url', 'https://hooks.slack.com/services/test/test/test');
        Config::set('app.name', null);
        Config::set('slack-new-user-notifier.app_name', 'Test App from slack-new-user-notifier.app_name');

        // Mock HTTP response
        Http::fake();

        $email = 'testuser@example.com';
        SlackNotifier::notify($email);

        $maskedEmail = 'te***@example.com';
        Http::assertSent(function ($request) use ($maskedEmail) {
            return $request['text'] === 'Test App from slack-new-user-notifier.app_name - New user registration: ' . $maskedEmail;
        });
    }

    /** @test */
    public function it_masks_email_correctly()
    {
        // Test maskEmail method with different emails
        $email = 'test@gmail.com';
        $maskedEmail = SlackNotifier::maskEmail($email);
        $this->assertEquals('te***@gmail.com', $maskedEmail);

        $email = 'blablablabla@gmail.com';
        $maskedEmail = SlackNotifier::maskEmail($email);
        $this->assertEquals('bl***@gmail.com', $maskedEmail);

        $email = 'a@gmail.com';
        $maskedEmail = SlackNotifier::maskEmail($email);
        $this->assertEquals('a***@gmail.com', $maskedEmail);
    }
}
