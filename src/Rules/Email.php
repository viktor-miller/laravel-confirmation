<?php

namespace ViktorMiller\LaravelConfirmation\Rules;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth as AuthFacade;
use ViktorMiller\LaravelConfirmation\ShouldConfirmEmailInterface;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class Email
{
    /**
     * 
     * @param  string $attribute
     * @param  string $value
     * @param  array  $parameters
     * @return bool
     */
    public function confirmed($attribute, $value, array $parameters)
    {
        $credentials = [$attribute => $value];
        $provider    = $this->guard()->getProvider();
        $user = $provider->retrieveByCredentials($credentials);
        
        if ($user instanceof ShouldConfirmEmailInterface && ! $user->isConfirmed()) {
            if (isset($parameters['0'])) {
                $diff = Carbon::now()->diffInHours($user->created_at);
                $max  = $parameters['0'];
            
                return $diff < $max;
            }
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Get authenticated user instance
     * 
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function user()
    {
        return AuthFacade::user();
    }
    
    /**
     * 
     * @return mixed
     */
    protected function guard()
    {
        return AuthFacade::guard();
    }
}
