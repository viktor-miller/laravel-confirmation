<?php

namespace ViktorMiller\LaravelConfirmation;

use ViktorMiller\LaravelConfirmation\Notifications\Confirmation as ConfirmationNotification;

/**
 *
 * @author viktormiller
 */
trait ShouldConfirmEmail 
{
    /**
     * Determine if email already confirmed
     * 
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }
    
    /**
     * Get the e-mail address where confirmation links are sent.
     * 
     * @return type
     */
    public function getConfirmationEmail()
    {
        return $this->email;
    }
    
    /**
     * Send the email confirmation notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendConfirmationNotification($token)
    {
        $this->notify(new ConfirmationNotification($token));
    }
}
