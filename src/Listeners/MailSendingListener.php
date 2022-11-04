<?php

namespace Timedoor\MailLogger\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Timedoor\MailLogger\Logger\MailableLogger;
use Timedoor\MailLogger\Logger\MailLogger;

class MailSendingListener
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
        MailLogger::handleMailSendingEvent(new MailableLogger, $event);
    }
}
