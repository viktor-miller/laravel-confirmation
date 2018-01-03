<?php

namespace ViktorMiller\LaravelConfirmation;

/**
 *
 * @author viktormiller
 */
interface ShouldConfirmEmailInterface 
{
    /**
     * Determine if email already confirmed
     * 
     * @return bool
     */
    public function isConfirmed();
    
    /**
     * Get the e-mail address where confirmation links are sent.
     *
     * @return string
     */
    public function getConfirmationEmail();
    
    /**
     * Send the email confirmation notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendConfirmationNotification($token);
}
