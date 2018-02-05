<?php

namespace ViktorMiller\LaravelConfirmation;

use Closure;
use UnexpectedValueException;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Auth\UserProvider;
use ViktorMiller\LaravelConfirmation\Contracts\Confirmable;
use ViktorMiller\LaravelConfirmation\Contracts\TokenRepository;
use ViktorMiller\LaravelConfirmation\Contracts\Broker as BrokerContract;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class Broker implements BrokerContract
{
    /**
     * The email token repository.
     *
     * @var TokenRepository
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * Create a new email broker instance.
     *
     * @param  TokenRepository $tokens
     * @param  \Illuminate\Contracts\Auth\UserProvider $users
     * @return void
     */
    public function __construct(TokenRepository $tokens, UserProvider $users)
    {
        $this->users = $users;
        $this->tokens = $tokens;
    }

    /**
     * Send a confirm email link to a user.
     *
     * @param  string|Confirmable $email
     * @return string
     * @throw  InvalidArgumentException
     */
    public function send($email)
    {   
        $user = $email instanceof Confirmable 
                ? $email
                : $this->getUser(compact('email'));
        
        if (is_string($user)) {
            return $user;
        }
        
        if ($user->isConfirmed()) {
            return static::INVALID_USER;
        }

        $user->sendConfirmationNotification($this->createToken($user));

        return static::CONFIRM_LINK_SENT;
    }

    /**
     * Confirm email for the given token.
     *
     * @param  string|Confirmable $email
     * @param  string $token
     * @param  Closure $callback
     * @return string
     */
    public function confirm($email, $token, Closure $callback)
    {
        $user = $email instanceof Confirmable 
                ? $email
                : $this->getUser(compact('email'));
        
        if (is_string($user)) {
            return $user;
        }
        
        if ($user->isConfirmed()) {
            $this->deleteToken($user);
            return static::INVALID_USER;
        }
        
        if (! $this->existsToken($user, $token)) {
            return static::INVALID_TOKEN;
        }

        $callback($user);

        $this->deleteToken($user);

        return static::EMAIL_CONFIRMED;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array $credentials
     * @return Confirmable
     *
     * @throws UnexpectedValueException
     */
    protected function getUser(array $credentials)
    {
        $user = $this->users->retrieveByCredentials($credentials);

        if (! $user) {
            return static::INVALID_USER;
        } elseif (! $user instanceof Confirmable) {
            throw new UnexpectedValueException(
                sprintf('User must implement %s contract.', Confirmable::class)
            );
        }

        return $user;
    }

    /**
     * Create a new email confirm token for the given user.
     *
     * @param  Confirmable $user
     * @return string
     */
    public function createToken(Confirmable $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete email confirm tokens of the given user.
     *
     * @param  Confirmable $user
     * @return void
     */
    public function deleteToken(Confirmable $user)
    {
        $this->tokens->delete($user);
    }
    
    /**
     * Determine if token for given user exist
     * 
     * @param  Confirmable $user
     * @param  string $token
     * @return bool
     */
    public function existsToken(Confirmable $user, $token)
    {
        return $this->tokens->exists($user, $token);
    }

    /**
     * Get the email confirm token repository implementation.
     *
     * @return TokenRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->tokens;
    }
    
    /**
     * Init confirmation routes
     * 
     * @return void
     */
    public function routes()
    {
        Route::get('/email/confirmation', 'Auth\SendEmailConfirmationController@index');
        Route::post('/email/confirmation', 'Auth\SendEmailConfirmationController@send');
        Route::get('/email/confirmation/manual', 'Auth\ConfirmEmailController@index')->name('confirmation.manual');
        Route::get('/email/confirmation/auto', 'Auth\ConfirmEmailController@confirm')->name('confirmation.auto');
    }
}
