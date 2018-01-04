<?php

namespace ViktorMiller\LaravelConfirmation;

use Closure;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
interface EmailBrokerInterface 
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const CONFIRM_LINK_SENT = 'confirmation::alert.success.sent';

    /**
     * Constant representing a successfully confirm email.
     *
     * @var string
     */
    const EMAIL_CONFIRMED = 'confirmation::alert.success.confirmed';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'confirmation::alert.fail.user';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'confirmation::alert.fail.token';

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
