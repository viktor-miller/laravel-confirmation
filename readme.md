## Laravel Email Confirmation ##

This package is to add email confirmation to Laravel 5.4/5.5 project.

The package add a listener to event **Illuminate\Auth\Events\Registered**. When this event is fired then a record with unique token will be created and a notification with confirmation link will be sent. After click on verification link will user be confirmed and his token record will be deleted.

### Features ###
- create a migration to add "confirmed" column to users table
- create a migration to create "email_confirmations" table
- scaffold for controller, routes and notification
- publish views, translations and configs
- multilanguage support
- validation rule to block login or password reset for not confirmed user
- validation rule support optional property to set pause in hours. For example: after registration user allow login next 24 hours without email confirmation.
- form to resend notification with confirmation link


### Installation ###

Add package to your **composer.json** file:

	composer require viktor-miller/laravel-confirmation
	
For Laravel <= 5.4 add service provider and aliace to **config/app.php**

	'providers' => [
		...
		ViktorMiller\LaravelConfirmation\Providers\ServiceProvider::class,
		...
	],

Add a **ShouldConfirmEmail** trait and implement **ShouldConfirmEmailInterface** interface on your User model

	<?php
	
	...
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use ViktorMiller\LaravelConfirmation\ShouldConfirmEmail;
	use ViktorMiller\LaravelConfirmation\ShouldConfirmEmailInterface;
	
	class User extends Authenticatable implements ShouldConfirmEmailInterface
	{
	    use Notifiable, ShouldConfirmEmail;
	    
	    ...

Change trait reference in **App\Http\Controller\Auth\LoginController**

	<?php

	namespace App\Http\Controllers\Auth;
	
	use App\Http\Controllers\Controller;
	use ViktorMiller\LaravelConfirmation\Http\Controllers\AuthenticatesUsers;
	
	class LoginController extends Controller
	{
	    use AuthenticatesUsers;
	    
	    ...

Change trait reference in **App\Http\Controller\Auth\ForgotPasswordController**

	<?php

	namespace App\Http\Controllers\Auth;
	
	use App\Http\Controllers\Controller;
	use ViktorMiller\LaravelConfirmation\Http\Controllers\SendsPasswordResetEmails;
	
	class ForgotPasswordController extends Controller
	{
	    use SendsPasswordResetEmails;
	    
	    ...

Make a migration to add columns on users table

	php artisan migrate
	
Run artisan confirmation command
	
	php artisan confirmation

### Publish ###

If you want to do some changes or add a language you can publish translations

	php artisan vendor:publish --tag=confirmation:translations
	
If you want to do some changes on views you can publish views

	php artisan vendor:publish --tag=confirmation:views

if you want to do some changes on config you can publish config

	php artisan vendor:publish --tag=confirmation:config
	
### Console ###
supported options

	php artisan confirmation -h
	
### Validation ###
If you want to set pause for confirmation validation override **validateLogin** method in **App\Http\Controller\Auth\LoginController** and **validateEmail** in **App\Http\Controller\Auth\ForgotPasswordController**.

For expample 24 Hours

	/**
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|email_confirmed:24'
        ], [
            'email_confirmed' => trans('confirmation::validation.not_confirmed')
        ]);
    }
