<?php

namespace ViktorMiller\LaravelConfirmation\Http\Controllers;

use Illuminate\Http\Request;
use ViktorMiller\LaravelConfirmation\Contracts\Broker;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
trait SendConfirmation
{
    use ConfirmationBroker;
    
    /**
     * @var string
     */
    protected $redirectTo = '/email/confirmation/manual';
    
    /**
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.emails.send');
    }
    
    /**
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
     * 
     * @param  Request $request 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendedResponse(Request $request, $response)
    {
        return redirect()->route('confirmation.manual', [
            'email' => $request->input('email')
        ])->with('success', trans($response));
    }
    
    /**
     * 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function notSendedResponse($response)
    {
        return back()->with('error', trans($response));
    }
}
