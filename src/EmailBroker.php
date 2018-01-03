<?php

namespace ViktorMiller\LaravelConfirmation;

use Closure;
use UnexpectedValueException;
use Illuminate\Contracts\Auth\UserProvider;
use ViktorMiller\LaravelConfirmation\Repository\TokenRepositoryInterface;

/**
 * Description of EmailBroker
 *
 * @author viktormiller
 */
class EmailBroker implements EmailBrokerInterface
{
    /**
     * The email token repository.
     *
     * @var TokenRepositoryInterface
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
     * @param  TokenRepositoryInterface $tokens
     * @param  \Illuminate\Contracts\Auth\UserProvider $users
     * @return void
     */
    public function __construct(TokenRepositoryInterface $tokens,
                                UserProvider $users)
    {
        $this->users = $users;
        $this->tokens = $tokens;
    }

    /**
     * Send a confirm email link to a user.
     *
     * @param  array $credentials
     * @return string
     */
    public function send(array $credentials)
    {
        $user = $this->getUserByCredentials($credentials);

        if (is_null($user) || $user->isConfirmed()) {
            return static::INVALID_USER;
        }
        
        $user->sendConfirmationNotification(
            $this->tokens->create($user)
        );

        return static::CONFIRM_LINK_SENT;
    }

    /**
     * Confirm email for the given token.
     *
     * @param  string   $token
     * @param  Closure $callback
     * @return mixed
     */
    public function confirm($token, Closure $callback)
    {
        $user = $this->validateConfirm($token);
        
        if (is_string($user)) {
            return $user;
        }

        if (! $user instanceof ShouldConfirmEmailInterface) {
            return static::INVALID_USER;
        }
        
        if ($user->isConfirmed()) {
            $this->tokens->delete($user);
            return static::INVALID_USER;
        }

        $callback($user);

        $this->tokens->delete($user);

        return static::EMAIL_CONFIRMED;
    }

    /**
     * Validate a email confirm for the given token.
     *
     * @param  string $token
     * @return string|ShouldConfirmEmailInterface
     */
    protected function validateConfirm($token)
    {   
        if (is_null($row = $this->tokens->retriveByToken($token))) {
            return static::INVALID_TOKEN;
        }
        
        $user = $this->users->retrieveByCredentials([
            'email' => $row->email
        ]);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        return $user;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array $credentials
     * @return null|ShouldConfirmEmailInterface
     *
     * @throws UnexpectedValueException
     */
    protected function getUserByCredentials(array $credentials)
    {
        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof ShouldConfirmEmailInterface) {
            throw new UnexpectedValueException(
                sprintf('User must implement %s interface.', ShouldConfirmEmailInterface::class)
            );
        }

        return $user;
    }

    /**
     * Create a new email confirm token for the given user.
     *
     * @param  ShouldConfirmEmailInterface $user
     * @return string
     */
    protected function createToken(ShouldConfirmEmailInterface $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete email confirm tokens of the given user.
     *
     * @param  ShouldConfirmEmailInterface $user
     * @return void
     */
    protected function deleteToken(ShouldConfirmEmailInterface $user)
    {
        $this->tokens->delete($user);
    }
    
    /**
     * 
     * @param  ShouldConfirmEmailInterface $user
     * @return null|string
     */
    public function getToken(ShouldConfirmEmailInterface $user)
    {
        $row = $this->tokens->retriveByUser($user);
        
        return $row ? $row->token : null;
    }

    /**
     * Get the email confirm token repository implementation.
     *
     * @return TokenRepositoryInterface
     */
    public function getRepository()
    {
        return $this->tokens;
    }
}
