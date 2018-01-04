<?php

namespace ViktorMiller\LaravelConfirmation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails as BeseSendsPasswordResetEmails;

/**
 * @author Viktor Miller <v.miller@forty-four.de>
 */
trait SendsPasswordResetEmails
{
    use BeseSendsPasswordResetEmails;
    
    /**
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|email_confirmed'
        ], [
            'email_confirmed' => trans('confirmation::validation.not_confirmed')
        ]);
    }
}
