<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetMailNotification extends Notification
{
    use Queueable;

    private $token;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $subject  = "Reset your password";
        $template = "mail.reset_password";

        if (request('platform') === 'web') {
            $link = "https://tuputime.com/reset-password?token=" . $this->token;
        } else {
            $link = "https://app.tuputime.com/?link=" . urlencode("https://app.tuputime.com/reset-password?token=" . $this->token) . "&apn=com.iotait.tuputime&ibi=com.tuputime";
        }

        $data = [
            'name'     => $notifiable->name,
            'restLink' => $link
        ];

        return (new MailMessage)
            ->subject($subject)
            ->view($template, $data);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
