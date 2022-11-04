<?php

namespace Timedoor\MailLogger\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Timedoor\MailLogger\Logger\MailLogger;
use Timedoor\MailLogger\Logger\NotificationLogger;

class NotificationSendingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->channel === 'mail') {
            MailLogger::handleMailSendingEvent(new NotificationLogger, $event);
        }
    }
}
