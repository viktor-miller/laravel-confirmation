<?php

namespace ViktorMiller\LaravelConfirmation\Repository;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use ViktorMiller\LaravelConfirmation\Contracts\Confirmable;
use ViktorMiller\LaravelConfirmation\Contracts\TokenRepository;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class DatabaseTokenRepository implements TokenRepository 
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The Hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /**
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * Create a new token repository instance.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $table
     * @param  string  $hashKey
     * @param  int  $expires
     * @return void
     */
    public function __construct(ConnectionInterface $connection, 
                                HasherContract $hasher,$table, $hashKey, 
                                $expires = 60)
    {
        $this->table = $table;
        $this->hasher = $hasher;
        $this->hashKey = $hashKey;
        $this->expires = $expires * 60;
        $this->connection = $connection;
    }

    /**
     * Create a new token record.
     *
     * @param  Confirmable $user
     * @return string
     */
    public function create(Confirmable $user)
    {
        $this->deleteExisting($user);

        $token = $this->createNewToken();
        
        $this->getTable()->insert($this->getPayload($user, $token));

        return $token;
    }

    /**
     * Delete all existing confirm tokens from the database.
     *
     * @param  Confirmable $user
     * @return int
     */
    protected function deleteExisting(Confirmable $user)
    {
        return $this->getTable()
                ->where('email', $user->confirmationEmail())
                ->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param  Confirmable $user
     * @param  string $token
     * @return array
     */
    protected function getPayload(Confirmable $user, $token)
    {
        return [
            'email' => $user->confirmationEmail(), 
            'token' => $this->hasher->make($token), 
            'created_at' => new Carbon
        ];
    }
    
    /**
     * Determine if a token record exists and is valid.
     *
     * @param  Confirmable $user
     * @param  string $token
     * @return bool
     */
    public function exists(Confirmable $user, $token)
    {
        $record = $this->last($user);

        return $record &&
               ! $this->tokenExpired($record->created_at) &&
                 $this->hasher->check($token, $record->token);
    }
    
    /**
     * 
     * @param  Confirmable $user
     * @return array
     */
    public function last(Confirmable $user)
    {   
        return $this->getTable()->where(
            'email', $user->confirmationEmail()
        )->first();
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * Delete a token record by user.
     *
     * @param  Confirmable $user
     * @return int
     */
    public function delete(Confirmable $user)
    {
        return $this->deleteExisting($user);
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        return $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    protected function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    /**
     * Get the database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Get the hasher instance.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }
}
