<?php

namespace ViktorMiller\LaravelConfirmation\Http\Controllers;

use Illuminate\Http\Request;
use ViktorMiller\LaravelConfirmation\Contracts\Broker;
use ViktorMiller\LaravelConfirmation\Facades\Confirmation;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
trait SendsConfirmationEmails
{   
    /**
     * Display the form to request a confirmation link.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.emails.send');
    }
    
    /**
     * Send a confirmation link to the given user.
     * 
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        $this->validateRequest($request);
        
        $response = $this->broker()->send($request->input('email'));
        
        return $response == Broker::CONFIRM_LINK_SENT
                ? $this->sendedResponse($request, $response)
                : $this->notSendedResponse($response);
    }
    
    /**
     * Validate the email for the given request.
     * 
     * @param  Request $request
     * @return void
     */
    protected function validateRequest(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);
    }
    
    /**
     * Get the response for a successful confirmation link.
     * 
     * @param  Request $request 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendedResponse(Request $request, $response)
    {
        return redirect()->route('confirmation.manual', [
            'email' => $request->input('email')
        ])->with('info', trans($response));
    }
    
    /**
     * Get the response for a failed confirmation link.
     * 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function notSendedResponse($response)
    {
        return back()->withInput()->with('error', trans($response));
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
