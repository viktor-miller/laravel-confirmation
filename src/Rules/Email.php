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
            if (array_has($parameters, 0) && $created = $user->getAttribute('created_at')) {
                return Carbon::now()->diffInHours($created) < array_get($parameters, 0);
            }
            
            return false;
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
