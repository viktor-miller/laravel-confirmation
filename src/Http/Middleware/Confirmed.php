<?php

namespace ViktorMiller\LaravelConfirmation\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use ViktorMiller\LaravelConfirmation\Facades\Confirmation;
use ViktorMiller\LaravelConfirmation\Contracts\Confirmable;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class Confirmed 
{   
    /**
     * @var Confirmable
     */
    protected $user;
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {   
        if ($this->user() && ! $this->user()->isConfirmed()) {
            $this->guard()->logout();

            if ($request->ajax()) {
                return response('Unauthenticated', 401);
            }
            
            return redirect()
                    ->route('confirmation.manual', [
                        'email' => $this->user()->confirmationEmail()
                    ])
                    ->with('error', trans('confirmation::alert.unconfirmed'));
        }
        
        return $next($request);
    }
    
    /**
     * Get authenticate user
     * 
     * @return \Illuminate\Contracts\Auth\Authenticable
     */
    protected function user()
    {
        if (! $this->user) {
            if ($this->guard()->user() instanceof Confirmable) {
                $this->user = $this->guard()->user();
            }
        }
        
        return $this->user;
    }
    
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
    
    /**
     * Get confirmation broker
     * 
     * @return \ViktorMiller\LaravelConfirmation\Contracts\Broker
     */
    protected function broker()
    {
        return Confirmation::broker();
    }
}
