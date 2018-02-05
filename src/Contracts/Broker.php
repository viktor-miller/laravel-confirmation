<?php

namespace ViktorMiller\LaravelConfirmation\Contracts;

use Closure;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
interface Broker
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
     * @param  string $email
     * @return string
     */
    public function send($email);

    /**
     * Confirm email for the given token.
     *
     * @param  string  $email
     * @param  string  $token
     * @param  Closure $callback
     * @return string
     */
    public function confirm($email, $token, Closure $callback);
    
    /**
     * Create new token
     * 
     * @param \ViktorMiller\LaravelConfirmation\Contracts\Confirmable $user
     */
    public function createToken(Confirmable $user);
    
    /**
     * Determine if token exists
     * 
     * @param  \ViktorMiller\LaravelConfirmation\Contracts\Confirmable $user
     * @param  string $token
     * @return bool
     */
    public function existsToken(Confirmable $user, $token);
    
    /**
     * Delete token
     * 
     * @param \ViktorMiller\LaravelConfirmation\Contracts\Confirmable $user
     */
    public function deleteToken(Confirmable $user);
    
    /**
     * Init confirmation routes
     * 
     * @return void 
     */
    public function routes();
}
