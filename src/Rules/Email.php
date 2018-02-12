<?php

namespace ViktorMiller\LaravelConfirmation\Rules;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth as AuthFacade;
use ViktorMiller\LaravelConfirmation\Contracts\Confirmable;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class Email
{
    /**
     * Determine if the validation rule passes.
     * 
     * @param  string $attribute
     * @param  string $value
     * @param  array  $parameters
     * @return bool
     */
    public function confirmed($attribute, $value, array $parameters)
    {
        $user = $this->guard()->getProvider()->retrieveByCredentials([
            $attribute => $value
        ]);
        
        if ($user instanceof Confirmable && ! $user->isConfirmed()) {
            $pause = array_get($parameters, 0, config('confirmation.pause', 0));
            
            return Carbon::now()->diffInHours($user->createdAt()) < $pause;
        }
        
        return true;
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
