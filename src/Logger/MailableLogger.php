<?php

namespace Timedoor\MailLogger\Logger;

use Timedoor\MailLogger\Models\MailLog;
use Illuminate\Support\Facades\Mail;
use Timedoor\MailLogger\Contracts\MailLoggerContract;

class MailableLogger implements MailLoggerContract
{
    /**
     * @param \Illuminate\Mail\Events\MessageSending $event
     */
    public static function handleMailSendingEvent($event)
    {
        // If recipients is empty, It means mail is being sent using notification
        // We ignore this mail because it will be handled by the notification listener
        if (isset($event->data['__mail_log_uuid']) && count($event->data['__mail_log_recipients']) > 0) {
            // if the __mail_log_id is set, It means the mail
            // is a resend. So We can increment the tries on
            // the original mail without duplicating it.
            if (isset($event->data['__mail_log_id'])) {
                $mail = MailLog::find($event->data['__mail_log_id']);
            }

            if (isset($mail) && $mail) {

                $mail->update([
                    'is_sent' => false,
                    'tries' => $mail->tries + 1,
                    'body' => $event->data['__mail_log_body'] ?? '',
                ]);
            } else {

                MailLog::create([
                    'uuid' => $event->data['__mail_log_uuid'],
                    'recipients' =>  $event->data['__mail_log_recipients'] ?? [],
                    'subject'  => $event->data['__mail_log_subject'] ?? '',
                    'mailable_name' => $event->data['__mail_log_mailable_name'] ?? '',
                    'body' => $event->data['__mail_log_body'] ?? '',
                    'mailable' => $event->data['__mail_log_mailable'] ?? null,
                    'is_queued' => $event->data['__mail_log_queued'] ?? false,
                    'is_sent' => false,
                    'tries' => 1
                ]);
            }
        }
    }

    /**
     * @param \Illuminate\Mail\Events\MessageSent $event
     */
    public static function handleMailSentEvent($event)
    {
        // If recipients is empty, It means mailable is part of a notification
        // which will be handled by the notification listener
        if (isset($event->data['__mail_log_uuid']) && count($event->data['__mail_log_recipients']) > 0) {

            if (isset($event->data['__mail_log_id'])) {
                MailLog::whereId($event->data['__mail_log_id'])->update(['is_sent' => true]);
            } else {
                MailLog::whereUuid($event->data['__mail_log_uuid'])->update(['is_sent' => true]);
            }
        }
    }

    /**
     * @param \Timedoor\MailLogger\Models\MailLog $mail
     * @param bool $sync
     */
    public static function resendMail(MailLog $mail, $sync = false)
    {
        /**
         * @var \Illuminate\Mail\Mailable $mailable
         */
        $mailable = unserialize($mail->mailable);

        Mail::send($mailable->with([
            '__mail_log_id' => $mail->id,
            '__mail_log_uuid' => $mail->uuid
        ]));
    }
}
