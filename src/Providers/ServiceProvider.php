<?php

namespace ViktorMiller\LaravelConfirmation\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use ViktorMiller\LaravelConfirmation\Facades\Email;
use ViktorMiller\LaravelConfirmation\Console\Commands;
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
     * Current package root path
     * 
     * @var string
     */
    protected $root;
    
    /**
     * Create a new service provider instance.
     * 
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        
        $this->root = __DIR__ .'/../../';
    }
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Configuration
        $this->publishes([
            $this->root .'config/confirmation.php' => config_path('confirmation.php'),
        ]);
        
        // Translations
        $this->loadTranslationsFrom($this->root .'resources/lang', 'confirmation');
        $this->publishes([
            $this->root .'resources/lang' => resource_path('lang/vendor/confirmation'),
        ]);
        
        // Migration
        $this->loadMigrationsFrom($this->root . 'database/migrations');
        
        // Views
        $this->loadViewsFrom($this->root .'/resources/views', 'confirmation');
        
        $this->publishes([
            $this->root .'/resources/views' => resource_path('views/vendor/confirmation'),
        ]);
        
        $this->initEventListeners();
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
            $this->root .'/config/confirmation.php', 'confirmation'
        );
        
        $this->app->registerDeferredProvider(EmailServiceProvider::class);
    }
    
    /**
     * Init event listeners
     * 
     * @return void
     */
    protected function initEventListeners()
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
}
