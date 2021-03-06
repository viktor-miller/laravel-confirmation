<?php

namespace ViktorMiller\LaravelConfirmation;

use App\Notifications\Auth\Confirmation;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
trait Confirmable 
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
    public function confirmationEmail()
    {
        return $this->email;
    }
    
    /**
     * Gte created at
     * 
     * @return \Carbon\Carbon
     */
    public function createdAt()
    {
        return $this->created_at;
    }
    
    /**
     * Send the email confirmation notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendConfirmationNotification($token)
    {
        $this->notify(new Confirmation($this->confirmationEmail(), $token));
    }
}
