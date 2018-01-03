<?php

namespace ViktorMiller\LaravelConfirmation;

use App\Notifications\Auth\Confirmation as ConfirmationNotification;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
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
