<?php

namespace ViktorMiller\LaravelConfirmation;

use Closure;

/**
 *
 * @author viktormiller
 */
interface EmailBrokerInterface 
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const CONFIRM_LINK_SENT = 'confirmation.sent';

    /**
     * Constant representing a successfully confirm email.
     *
     * @var string
     */
    const EMAIL_CONFIRMED = 'confirmation.confirmed';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'confirmation.user';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'confirmation.token';

    /**
     * Send a confirm link to a user.
     *
     * @param  array $credentials
     * @return string
     */
    public function send(array $credentials);

    /**
     * Confirm email for the given token.
     *
     * @param  string    $token
     * @param  Closure  $callback
     * @return mixed
     */
    public function confirm($token, Closure $callback);
    
    /**
     * Get token by user instance
     * 
     * @param ShouldConfirmEmailInterface $user
     * @return null|string
     */
    public function getToken(ShouldConfirmEmailInterface $user);
}
