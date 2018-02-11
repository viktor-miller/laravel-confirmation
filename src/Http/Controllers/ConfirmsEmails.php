<?php

namespace ViktorMiller\LaravelConfirmation\Http\Controllers;

use Illuminate\Http\Request;
use ViktorMiller\LaravelConfirmation\Contracts\Broker;
use ViktorMiller\LaravelConfirmation\Facades\Confirmation;
use ViktorMiller\LaravelConfirmation\Contracts\Confirmable;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
trait ConfirmsEmails
{   
    /**
     * Display the confirmation form.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('auth.emails.confirm', [
            'email' => $request->input('email'),
            'token' => $request->input('token')
        ]);
    }
    
    /**
     * Confirm the given user's email.
     * 
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {   
        $this->validate($request, $this->rules(), $this->validationErrorMessages());
        
        $response = $this->broker()->confirm($request->input('email'), 
                $request->input('token'), function ($user) {
                $this->confirmUser($user);
            }
        );
        
        return $response == Broker::EMAIL_CONFIRMED
                ? $this->sendConfirmedResponse($response)
                : $this->sendNotConfirmedResponse($response);
    }
    
    /**
     * Get the email confirm validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'email' => 'required|email',
            'token' => 'required'
        ];
    }
    
    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }
    
    /**
     * Confirm the given user's email.
     * 
     * @param  Confirmable $user
     * @return void
     */
    protected function confirmUser(Confirmable $user)
    {
        $user->confirmed = true;
        $user->save();
    }
    
    /**
     * Get the response for a successful email confirmation.
     * 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendConfirmedResponse($response)
    {
        return redirect('/')
                ->with('success', trans($response));
    }
    
    /**
     * Get the response for a failed email confirmation.
     * 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendNotConfirmedResponse($response)
    {
        return redirect()
                ->route('confirmation.manual')
                ->withInput()
                ->with('error', trans($response));
    }
    
    /**
     * Get the broker to be used during email confirmation.
     * 
     * @return \ViktorMiller\LaravelConfirmation\Contracts\Broker
     */
    protected function broker()
    {
        return Confirmation::broker();
    }
}
