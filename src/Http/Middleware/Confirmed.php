<?php

namespace ViktorMiller\LaravelConfirmation\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @author Viktor Miller <v.miller@forty-four.de>
 */
class Confirmed 
{   
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        if ($this->autnenticated() && ! $this->isNotConfirmed()) {
            $this->guard()->logout();
            
            return $request->ajax()
                    ? response('Unauthenticated', 401)
                    : redirect()->refresh();
        }
        
        return $next($request);
    }
    
    /**
     * Determine if user is not confirmed
     * 
     * @return bool
     */
    protected function isNotConfirmed()
    {
        $user = $this->user();
        $pause = config('confirmation.pause', 0);
        
        return $user instanceof Confirmable && 
             ! $user->isConfirmed() && 
               Carbon::now()->diffInHours($user->createdAt()) >= $pause;
    }
    
    /**
     * Determine is user authenticated
     * 
     * @return bool
     */
    protected function autnenticated()
    {
        return $this->guard()->check();
    }
    
    /**
     * Get authenticated user instance
     * 
     * @return \Illuminate\Contracts\Auth\Authenticable
     */
    protected function user()
    {
        return Auth::user();
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
}
