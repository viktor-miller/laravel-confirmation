<?php

namespace ViktorMiller\LaravelConfirmation\Providers;

use Illuminate\Support\Facades\Validator;
use ViktorMiller\LaravelConfirmation\BrokerManager;
use ViktorMiller\LaravelConfirmation\Contracts\Broker;
use ViktorMiller\LaravelConfirmation\Console\Commands;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use ViktorMiller\LaravelConfirmation\Contracts\BrokerManager as BrokerManagerContract;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class ServiceProvider extends BaseServiceProvider 
{   
    /**
     * @var string 
     */
    protected $packageRoot;
    
    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
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
        $this->initConfigPublish();
        $this->initTranslationPublish();
        $this->initMigrations();
        $this->initConsoleCommands();
        $this->initValidatorRules();
    }

    /**
     * Register any application services.
     * 
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        
        $this->app->singleton(BrokerManagerContract::class, function ($app) {
            return new BrokerManager($app);
        });
        
        $this->app->bind(Broker::class, function ($app) {
            return $app->make(BrokerManagerContract::class)->broker();
        });
    }
    
    /**
     * Init config publish
     */
    protected function initConfigPublish()
    {
        $this->publishes([
            $this->root .'config/confirmation.php' => config_path('confirmation.php'),
        ], 'confirmation:config');
    }
    
    /**
     * Init translation publish
     */
    protected function initTranslationPublish()
    {
        $path = $this->root .'resources/lang';
        
        $this->loadTranslationsFrom($path, 'confirmation');
        $this->publishes([
            $path => resource_path('lang/vendor/confirmation'),
        ], 'confirmation:translations');
    }
    
    /**
     * Init package migrations
     */
    protected function initMigrations()
    {
        $this->loadMigrationsFrom($this->root .'database/migrations');
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
     * 
     * @return void 
     */
    protected function initValidatorRules()
    {
        Validator::extend(
            'verified', 'ViktorMiller\LaravelConfirmation\Rules\Email@verified'
        );
    }
    
    /**
     * Merge package config
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(
            $this->root .'config/confirmation.php', 'confirmation'
        );
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [BrokerManagerContract::class, Broker::class];
    }
}
