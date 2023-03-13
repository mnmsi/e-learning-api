<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Env;

class SendMailNotification extends Notification
{
    use Queueable;

    private $isNewLearner;
    private $token;
    private $data;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($isNewLearner = true, $token = null, $data = null)
    {
        $this->isNewLearner = $isNewLearner;
        $this->token        = $token;
        $this->data         = $data;
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
        //If new user, then send welcome mail
        if ($this->isNewLearner) {
            $subject = "Welcome to " . Env::get('APP_NAME');

            //If new user and added by educator then send welcome mail with reset password link
            if (!is_null($this->token)) {
                $template = "mail.enroll_new_student";
                $data     = [
                    'name'     => $notifiable->name,
                    'restLink' => url("reset-password?token=" . $this->token)
                ];
            } //If user register, then send mail
            else {
                $template = "mail.new_user";
                $data     = [
                    'name' => $notifiable->name,
                ];
            }
        } //If user is exists,then send notify mail
        else {
            $subject  = "You're enrolled in a course by " . $this->data->course->educator->name;
            $template = "mail.enroll_student";
            $data     = [
                'name'           => $notifiable->name,
                'course_id'      => $this->data->course->id,
                'course_name'    => $this->data->course->name,
                'course_privacy' => $this->data->course->privacy,
                'educator_name'  => $this->data->course->educator->name,
            ];
        }

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
