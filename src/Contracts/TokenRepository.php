<?php

namespace ViktorMiller\LaravelConfirmation\Contracts;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
interface TokenRepository
{
    /**
     * Create a new token.
     *
     * @param  Confirmable $user
     * @return string
     */
    public function create(Confirmable $user);
    
    /**
     * Determine if a token record exists and is valid.
     *
     * @param  Confirmable $user
     * @param  string $token
     * @return bool
     */
    public function exists(Confirmable $user, $token);
    
    /**
     * Delete a token record.
     *
     * @param  Confirmable $user
     * @return int
     */
    public function delete(Confirmable $user);
    
    /**
     * Delete expired tokens.
     *
     * @return int
     */
    public function deleteExpired();
}
