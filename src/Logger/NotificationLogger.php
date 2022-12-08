<?php

namespace Timedoor\MailLogger\Logger;

use Timedoor\MailLogger\Models\MailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Timedoor\MailLogger\Contracts\MailLoggerContract;

class NotificationLogger implements MailLoggerContract
{
    /**
     * @param \Illuminate\Notifications\Events\NotificationSending $event
     * @return mixed
     */
    public static function handleMailSendingEvent($event)
    {
        $mail = MailLog::whereUuid($event->notification->id)->first();
        $newNotif = $event->notification->toMail($event->notifiable);

        if ($mail) {
            $mail->update([
                'is_sent' => false,
                'tries' => $mail->tries + 1,
                'body' => view($newNotif->view, $newNotif->viewData)->render(),
            ]);
        } else {
            $recipients = [];
            if ($event->notifiable instanceof AnonymousNotifiable) {
                if (is_array($event->notifiable->routes)) {
                    $recipients = array_values($event->notifiable->routes);
                }
            } else {
                $recipients = [$event->notifiable->routeNotificationFor('mail')];
            }

            MailLog::create([
                'uuid' => $event->notification->id,
                'recipients' => $recipients,
                'subject'  => $newNotif->subject,
                'mailable_name' => get_class($event->notification),
                'mailable' => serialize(clone $event->notification),
                'body' => $newNotif instanceof \Illuminate\Notifications\Messages\MailMessage ? $newNotif->render() : view($newNotif->view, $newNotif->viewData)->render(),
                'is_queued' => in_array(ShouldQueue::class, class_implements($event->notification)),
                'is_notification' => true,
                'notifiable' => serialize(clone $event->notifiable),
                'is_sent' => false,
                'tries' => 1
            ]);
        }
    }

    /**
     * @param \Illuminate\Notifications\Events\NotificationSent $event
     * @return mixed
     */
    public static function handleMailSentEvent($event)
    {
        MailLog::whereUuid($event->notification->id)->update(['is_sent' => true]);
    }

    /**
     * @param \Timedoor\MailLogger\Models\MailLog $mail
     * @param bool $sync
     */
    public static function resendMail(MailLog $mail, $sync = false)
    {
        if ($sync) {
            (unserialize($mail->notifiable))->notifyNow(unserialize($mail->notification));
        } else {
            (unserialize($mail->notifiable))->notify(unserialize($mail->notification));
        }
    }
}
