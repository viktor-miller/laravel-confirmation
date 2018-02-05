# Laravel Email Confirmation #

This package is intended to confirm the email address of the user. **Tested and used with Laravel 5.4 and 5.5**

## Features ##
- Migration to add "confirmed" column to users table
- Migration to create "email_confirmations" table
- Scaffold for view, controller, routes and notification
- Publish translations and configs
- The validation rule for unconfirmed users. The validation rule supports an additional property for setting a pause in hours. For example: after registration, the user is allowed to log in for (n) hours without confirming the email address.
- HTML form for resending a notification with instructions for confirming an email address
- HTML form for confirmation of email address in manual mode (Enter e-mail and token).
- Support for confirmation of the email address in the automatic mode (click on the link that was received by e-mail)


## Installation ##

1. Add package to your **composer.json** file:

	composer require viktor-miller/laravel-confirmation
	
2. For Laravel 5.4 add service provider and aliase to **config/app.php**

		'providers' => [
			...
			ViktorMiller\LaravelConfirmation\Providers\ServiceProvider::class,
			...
		],
		'aliases' => [
			...
			'Confirmation' => ViktorMiller\LaravelConfirmation\Facades\Confirmation::class

3. Add a **Confirmable** trait and implement **Confirmable** interface on your User model

		<?php
		
		...
		use Illuminate\Notifications\Notifiable;
		use Illuminate\Foundation\Auth\User as Authenticatable;
		use ViktorMiller\LaravelConfirmation\Confirmable;
		use ViktorMiller\LaravelConfirmation\Contracts\Confirmable as ConfirmableContract;
		
		class User extends Authenticatable implements Confirmable
		{
		    use Notifiable, Confirmable;
		    
		    ...

4. Add validation rule in LoginController and ForgotPasswordController to restrict users with an unconfirmed email address.

	For Laravel >= 5.4:

	LoginController

		<?php
		
		namespace App\Http\Controllers\Auth;
		
		class LoginController extends Controller
		{
			/**
		     * Validate the user login request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return void
		     */
		    protected function validateLogin(Request $request)
		    {
		        $this->validate($request, [
		            $this->username() => 'required|string|verified',
		            'password' => 'required|string',
		        ]);
		    }

	and ForgotPasswordController

		<?php
		
		namespace App\Http\Controllers\Auth;
		
		class ForgotPasswordController extends Controller
		{ 
			/**
		     * Validate the email for the given request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return void
		     */
		    protected function validateEmail(Request $request)
		    {
		        $this->validate($request, ['email' => 'required|email|verified']);
		    }
	    
	For Laravel 5.5:
	
	LoginController

		<?php
	
		namespace App\Http\Controllers\Auth;
		
		use ViktorMiller\LaravelConfirmation\Rules\Verified;
		
		class LoginController extends Controller
		{
			/**
		     * Validate the user login request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return void
		     */
		    protected function validateLogin(Request $request)
		    {
		        $this->validate($request, [
		            $this->username() => [
		                'required', 'string', new Verified
		            ],
		            'password' => 'required|string',
		        ]);
		    }
	    
	and ForgotPasswordController
	
		<?php
		
		namespace App\Http\Controllers\Auth;
		
		use ViktorMiller\LaravelConfirmation\Rules\Verified;
		    
		class ForgotPasswordController extends Controller
		{ 
			...
			/**
		     * Validate the email for the given request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return void
		     */
		    protected function validateEmail(Request $request)
		    {
		        $this->validate($request, ['email' => [
		        	'required', 'email', new Verified
		        ]);
		    }
	    ...
5. Add event listener to **Illuminate\Auth\Events\Registered**
	
		<?php
		
		namespace App\Providers;
	
		use Illuminate\Support\Facades\Event;
		use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
	
		class EventServiceProvider extends ServiceProvider
		{
		    /**
		     * The event listener mappings for the application.
		     *
		     * @var array
		     */
		    protected $listen = [
		        'Illuminate\Auth\Events\Registered' => [
		            'ViktorMiller\LaravelConfirmation\Listeners\EmailConfirmation'
		        ]
	        

6. Run migrations

		php artisan migrate
	
7. Run artisan confirmation command
	
		php artisan confirmation


## Publish ##

If you want to do some changes or add a language you can publish translations

	php artisan vendor:publish --tag=confirmation:translations

If you want to do some changes on config you can publish config

	php artisan vendor:publish --tag=confirmation:config
	
### Console ###
supported options

	php artisan confirmation -h
	
### Validation ###
If you want to allow users to ignore the verification rule "verified" for a certain number of hours (for example 24h):

for Laravel >= 5.4
	
    $this->validate($request, [
    	'email' => 'required|email|verified:24'
    ]);
    
for Laravel >= 5.5
	
	use ViktorMiller\LaravelConfirmation\Rules\Verified;
	
	$this->validate($request, [
   		'email' => [
      		'required', 'string', new Verified(24)
       ],
	]);
    
    
