<?php

namespace ViktorMiller\LaravelConfirmation\Rules;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Rule;
use ViktorMiller\LaravelConfirmation\Contracts\Confirmable;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class Verified implements Rule
{
    /**
     * @var integer 
     */
    protected $pause;
    
    /**
     * 
     * @param integer $pause
     */
    public function __construct($pause = null)
    {
        $this->pause = $pause;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = $this->guard()->getProvider()->retrieveByCredentials([
            $attribute => $value
        ]);
        
        if ($user instanceof Confirmable && ! $user->isConfirmed()) {
            if ($this->pause && $created = $user->getAttribute('created_at')) {
                return Carbon::now()->diffInHours($created) < $this->pause;
            }
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Get Auth's guard
     * 
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard();
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be confirmed.';
    }
}