<?php

namespace ViktorMiller\LaravelConfirmation\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use ViktorMiller\LaravelConfirmation\Facades\Email;
use ViktorMiller\LaravelConfirmation\Console\Commands;
use ViktorMiller\LaravelConfirmation\EmailBrokerManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use ViktorMiller\LaravelConfirmation\ShouldConfirmEmailInterface;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class ServiceProvider extends BaseServiceProvider 
{   
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $packagePath = __DIR__ .'/../../';
        
        // Configuration
        $this->publishes([
            $packagePath .'config/confirmation.php' => config_path('confirmation.php'),
        ]);
        
        // Translations
        $translationsPath = $packagePath .'resources/lang';
        
        $this->loadTranslationsFrom($translationsPath, 'confirmation');
        $this->publishes([
            $translationsPath => resource_path('lang/vendor/confirmation'),
        ], 'confirmation:translations');
        
        // Migration
        $this->loadMigrationsFrom($packagePath . 'database/migrations');
        
        $this->addEventListener();
        $this->initConsoleCommands();
    }

    /**
     * Register any application services.
     * 
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ .'/../../config/confirmation.php', 'confirmation'
        );
        
        $this->app->singleton('confirmation.email', function ($app) {
            return new EmailBrokerManager($app);
        });
        
        $this->app->bind('confirmation.email.broker', function ($app) {
            return $app->make('confirmation.email')->broker();
        });
    }
    
    /**
     * 
     * @return void
     */
    protected function addEventListener()
    {
        Event::listen(Registered::class, function(Registered $event) {
            if ($event->user instanceof ShouldConfirmEmailInterface) {
                Email::broker()->send([
                    'email' => $event->user->email
                ]);
            }
        });
    }
    
    /**
     * Init console commands
     * 
     * @return void
     */
    protected function initConsoleCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Confirmation::class
            ]);
        }
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
