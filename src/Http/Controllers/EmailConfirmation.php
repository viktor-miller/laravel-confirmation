<?php

namespace ViktorMiller\LaravelConfirmation\Http\Controllers;

use Illuminate\Http\Request;
use ViktorMiller\LaravelConfirmation\Contracts\Broker;
use ViktorMiller\LaravelConfirmation\Contracts\Confirmable;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
trait EmailConfirmation
{
    use ConfirmationBroker;
    
    /**
     * @var string
     */
    protected $redirectTo = '/email/confirmation';
    
    /**
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
     * 
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {   
        $this->validateRequest($request);
        
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
     * 
     * @param  Request $request
     * @return void
     */
    protected function validateRequest(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'token' => 'required'
        ]);
    }
    
    /**
     * 
     * @param Confirmable $user
     */
    protected function confirmUser(Confirmable $user)
    {
        $user->confirmed = true;
        $user->save();
    }
    
    /**
     * 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendConfirmedResponse($response)
    {
        return redirect('/')->with('success', trans($response));
    }
    
    /**
     * 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendNotConfirmedResponse($response)
    {
        return redirect($this->redirectTo)->with('error', trans($response));
    }
}
