<?php

namespace Timedoor\MailLogger;

use Timedoor\MailLogger\Commands\PruneOldMailables;
use Timedoor\MailLogger\Commands\ResendMail;
use Timedoor\MailLogger\Commands\ResendUnSentMail;
use Timedoor\MailLogger\Listeners\MailSendingListener;
use Timedoor\MailLogger\Listeners\MailSentListener;
use Timedoor\MailLogger\Listeners\NotificationSendingListener;
use Timedoor\MailLogger\Listeners\NotificationSentListener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Service provider
 * 
 * @package Timedoor\MailLogger
 */
class MailLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->listen(MessageSending::class, MailSendingListener::class);
        $this->app['events']->listen(MessageSent::class, MailSentListener::class);

        $this->app['events']->listen(NotificationSending::class, NotificationSendingListener::class);
        $this->app['events']->listen(NotificationSent::class, NotificationSentListener::class);

        $this->registerMailableData();

        $this->publishes([
            __DIR__ . '/Config/mail_logger.php' => config_path('mail_logger.php')
        ], 'mail-logger');

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        $this->commands([
            PruneOldMailables::class,
            ResendUnSentMail::class,
            ResendMail::class
        ]);
    }

    private function registerMailableData()
    {
        $existing_callback = Mailable::$viewDataCallback;

        Mailable::buildViewDataUsing(function ($mailable) use ($existing_callback) {

            $data = [];

            if ($existing_callback) {
                $data = call_user_func($existing_callback, $mailable);

                if (!is_array($data)) $data = [];
            }

            if (method_exists($mailable, 'getHtmlBody')) {
                $body = $mailable->getHtmlBody();
            } else {
                $body = $mailable instanceof \Illuminate\Notifications\Messages\MailMessage ? $mailable->render() : view($mailable->view, $mailable->viewData)->render();
            }

            return array_merge($data, [
                '__mail_log_uuid' => Str::uuid(),
                '__mail_log_mailable_name' => get_class($mailable),
                '__mail_log_subject' => $mailable->subject,
                '__mail_log_recipients' => collect($mailable->to)->pluck('address')->toArray(),
                '__mail_log_body' => $body,
                '__mail_log_mailable' => serialize(clone $mailable),
                '__mail_log_queued' => in_array(ShouldQueue::class, class_implements($mailable)),
            ]);
        });
    }
}
