<?php

namespace ViktorMiller\LaravelConfirmation\Repository;

use ViktorMiller\LaravelConfirmation\ShouldConfirmEmailInterface;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
interface TokenRepositoryInterface 
{
    /**
     * Create a new token.
     *
     * @param  ShouldConfirmEmailInterface $user
     * @return string
     */
    public function create(ShouldConfirmEmailInterface $user);
    
    /**
     * Retrive by token
     *
     * @param  string  $token
     * @return null|StdClass
     */
    public function retriveByToken($token);
    
    /**
     * Retrive by user
     *
     * @param  ShouldConfirmEmailInterface $user
     * @return null|StdClass
     */
    public function retriveByUser(ShouldConfirmEmailInterface $user);
    
    /**
     * Delete a token record.
     *
     * @param  ShouldConfirmEmailInterface $user
     * @return int
     */
    public function delete(ShouldConfirmEmailInterface $user);
    
    /**
     * Delete expired tokens.
     *
     * @return int
     */
    public function deleteExpired();
}
