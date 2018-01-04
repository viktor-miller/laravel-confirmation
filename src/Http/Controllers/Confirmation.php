<?php

namespace ViktorMiller\LaravelConfirmation\Http\Controllers;

use Illuminate\Http\Request;
use ViktorMiller\LaravelConfirmation\Facades\Email;
use ViktorMiller\LaravelConfirmation\EmailBrokerInterface;
use ViktorMiller\LaravelConfirmation\ShouldConfirmEmailInterface;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
trait Confirmation
{
    /**
     * Route name holder to redirect response
     * 
     * @var string
     */
    protected $redirectTo = 'confirmation::form';
    
    /**
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('confirmation::index');
    }
    
    /**
     * 
     * @param Request $request
     */
    public function send(Request $request)
    {
        $this->validateSendRequest($request);
        
        $response = $this->broker()->send($this->credentials($request));
        
        return $response == EmailBrokerInterface::CONFIRM_LINK_SENT
                ? $this->sendSendedResponse($response)
                : $this->sendNotSendedResponse($response);
    }
    
    /**
     * 
     * @param Request $request
     */
    protected function validateSendRequest(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);
    }
    
    /**
     * 
     * @param  Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }
    
    /**
     * 
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendSendedResponse($response)
    {
        return redirect()
                ->route($this->redirectTo)
                ->with('success', trans($response));
    }
    
    /**
     * 
     * @param  mixed $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendNotSendedResponse($response)
    {
        return redirect()
                ->route($this->redirectTo)
                ->with('error', trans($response));
    }
    
    /**
     * 
     * @param  string $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function confirm($token)
    {
        $response = $this->broker()->confirm(
            $token, function ($user) {
                $this->confirmUser($user);
            }
        );
        
        return $response == EmailBrokerInterface::EMAIL_CONFIRMED
                ? $this->sendConfirmedResponse($response)
                : $this->sendNotConfirmedResponse($response);
    }
    
    /**
     * 
     * @param ShouldConfirmEmailInterface $user
     */
    protected function confirmUser(ShouldConfirmEmailInterface $user)
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
        return redirect()
                ->route($this->redirectTo)
                ->with('success', trans($response));
    }
    
    /**
     * 
     * @param  mixed $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendNotConfirmedResponse($response)
    {
        return redirect()
                ->route($this->redirectTo)
                ->with('error', trans($response));
    }
    
    /**
     * 
     * @return \ViktorMiller\LaravelConfirmation\EmailBrokerInterface
     */
    protected function broker()
    {
        return Email::broker();
    }
}
