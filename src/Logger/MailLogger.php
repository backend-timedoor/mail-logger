<?php

namespace Timedoor\MailLogger\Logger;

use Timedoor\MailLogger\Contracts\MailLoggerContract;
use Timedoor\MailLogger\Models\MailLog;

class MailLogger
{
    /**
     * @param \Timedoor\MailLogger\Contracts\MailLoggerContract $mail_logger
     * @param $event
     */
    public static function handleMailSendingEvent(MailLoggerContract $mail_logger, $event)
    {
        if ($mail_logger instanceof NotificationLogger) {
            $mailClass = get_class($event->notification);
        } else {
            $mailClass = isset($event->data['__mail_log_mailable_name']) ? $event->data['__mail_log_mailable_name'] : null;
        }

        if (!is_array(config('mail_logger.ignore')) || !in_array($mailClass, config('mail_logger.ignore'))) {
            $mail_logger->handleMailSendingEvent($event);
        }
    }

    /**
     * @param \Timedoor\MailLogger\Contracts\MailLoggerContract $mail_logger
     * @param $event
     */
    public static function handleMailSentEvent(MailLoggerContract $mail_logger, $event)
    {
        if ($mail_logger instanceof NotificationLogger) {
            $mailClass = get_class($event->notification);
        } else {
            $mailClass = isset($event->data['__mail_log_mailable_name']) ? $event->data['__mail_log_mailable_name'] : null;
        }

        if (!is_array(config('mail_logger.ignore')) || !in_array($mailClass, config('mail_logger.ignore'))) {
            $mail_logger->handleMailSentEvent($event);
        }
    }

    /**
     * @param \Timedoor\MailLogger\Models\MailLog $mail
     * @param bool $sync
     */
    public static function resendMail(MailLog $mail, $sync = false)
    {
        if ($mail->is_notification) {
            NotificationLogger::resendMail($mail, $sync);
        } else {
            MailableLogger::resendMail($mail);
        }
    }

    /**
     * @param $id
     * @param bool $sync
     */
    public static function resendMailById($id, $sync = false)
    {
        $mail = MailLog::find($id);

        if ($mail) self::resendMail($mail, $sync);
    }

    /**
     * @param $uuid
     * @param bool $sync
     */
    public static function resendMailByUuid($uuid, $sync = false)
    {
        $mail = MailLog::whereUuid($uuid)->first();

        if ($mail) self::resendMail($mail, $sync);
    }

    /**
     * @param bool $sync
     * @return int
     */
    public static function resendUnsentMails($sync = false)
    {
        $mails = MailLog::whereIsSent(false)->get();

        foreach ($mails as $mail) {
            self::resendMail($mail, $sync);
        }

        return $mails->count();
    }

    /**
     * @param $date
     * @return int
     */
    public static function pruneMails($date)
    {
        return MailLog::where('created_at', '<=', $date)->delete();
    }
}
