<?php

namespace ViktorMiller\LaravelConfirmation\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * @author Viktor Miller <v.miller@forty-four.de>
 */
class Confirmation extends Notification
{
    /**
     *
     * @var string
     */
    protected $token;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('You have just registered successfully.')
            ->line('Please confirm your e-mail address.')
            ->action('Verify', url(
                config('app.url').route('confirmation', $this->token, false)
            ));
    }
}