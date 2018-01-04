<?php

namespace ViktorMiller\LaravelConfirmation\Providers;

use ViktorMiller\LaravelConfirmation\EmailBrokerManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class EmailServiceProvider extends BaseServiceProvider 
{   
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Register any application services.
     * 
     * @return void
     */
    public function register()
    {
        $this->app->singleton('confirmation.email', function ($app) {
            return new EmailBrokerManager($app);
        });
        
        $this->app->bind('confirmation.email.broker', function ($app) {
            return $app->make('confirmation.email')->broker();
        });
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['confirmation.email', 'confirmation.email.broker'];
    }
}
