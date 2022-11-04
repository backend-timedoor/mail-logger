<?php

namespace Timedoor\MailLogger\Contracts;

use Timedoor\MailLogger\Models\MailLog;

/**
 * Mail logger interface
 * 
 * @method mixed handleMailSendingEvent($event)
 * @method mixed handleMailSendingEvent($event) 
 * @method mixed resendMail(MailLogger $mail, $sync = false)
 */
interface MailLoggerContract
{
    /**
     * Handle event when sending email
     * 
     * @param $event
     * @return mixed
     */
    public static function handleMailSendingEvent($event);

    /**
     * Handle event when email sent
     * 
     * @param $event
     * @return mixed
     */
    public static function handleMailSentEvent($event);

    /**
     * @param \Timedoor\MailLogger\Models\MailLog $mail
     * @param bool $sync
     */
    public static function resendMail(MailLog $mail, $sync = false);
}
