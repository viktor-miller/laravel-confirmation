<?php

namespace ViktorMiller\LaravelConfirmation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers as AuthenticatesUsersBase;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
trait AuthenticatesUsers 
{
    use AuthenticatesUsersBase;
    
    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string|email_confirmed',
            'password' => 'required|string',
        ], [
            'email_confirmed' => trans('confirmation::validation.not_confirmed')
        ]);
    }
}
