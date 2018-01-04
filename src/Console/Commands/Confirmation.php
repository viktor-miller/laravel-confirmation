<?php

namespace ViktorMiller\LaravelConfirmation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class Confirmation extends Command
{
    use DetectsApplicationNamespace;
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'confirmation
                    {--controllers      : Only scaffold the confirmation controllers}
                    {--notifications    : Only scaffold the confirmation notifications}
                    {--routes           : Only scaffold the confirmation routes}
                    {--force            : Overwrite existing views by default}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic confirmation files';
    
    /**
     * The controllers that need to be exported.
     * 
     * @var array
     */
    protected $controllers = [
        'auth/ConfirmationController.stub' => 'Http/Controllers/Auth/ConfirmationController.php'
    ];
    
    /**
     * The notifications that need to be exported.
     * 
     * @var array
     */
    protected $notifications = [
        'Confirmation.stub' => 'Notifications/Auth/Confirmation.php'
    ];
    
    /**
     * The routes that need to be exported.
     * 
     * @var array
     */
    protected $routes = [
        'web.stub' => 'routes/web.php'
    ];
    
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('notifications')) {
            $this->exportNotifications();
        } elseif ($this->option('controllers')) {
            $this->exportControllers();
        } elseif ($this->option('routes')) {
            $this->exportRoutes();
        } else {
            $this->exportControllers();
            $this->exportNotifications();
            $this->exportRoutes();
        }

        $this->info('Confirmation scaffolding generated successfully.');
    }
    
    /**
     * Export the confirmation controllers
     * 
     * @return void
     */
    protected function exportControllers()
    {
        foreach ($this->controllers as $stub => $dest) {
            if (file_exists($controller = app_path($dest)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$dest}] controller already exists. Do you want to replace it?")) {
                    continue;
                }
            }
            
            if (! is_dir(dirname($controller))) {
                mkdir(dirname($controller), 0755, true);
            }
            
            file_put_contents(
                $controller,
                $this->compileControllerStub($stub)
            );
        }
    }
    
    /**
     * Export the confirmation notifications
     * 
     * @return void
     */
    protected function exportNotifications()
    {
        foreach ($this->notifications as $stub => $dest) {
            if (file_exists($notification = app_path($dest)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$dest}] notification already exists. Do you want to replace it?")) {
                    continue;
                }
            }
            
            if (! is_dir(dirname($notification))) {
                mkdir(dirname($notification), 0755, true);
            }
            
            file_put_contents(
                $notification,
                $this->compileNotificationStub($stub)
            );
        }
    }
    
    /**
     * Export the confirmation routes
     * 
     * @return void
     */
    protected function exportRoutes()
    {
        foreach ($this->routes as $stub => $dest) {
            if (file_exists($route = base_path($dest)) && 
                ! $this->option('force') && 
                strpos(file_get_contents(base_path($dest)), 'confirmation') !== FALSE
            ) {
                if (! $this->confirm("The file [{$dest}] already contains confirmation routes. Do you want to replace it?")) {
                    return;
                }
            }
            
            if (! is_dir(dirname($route))) {
                mkdir(dirname($route), 0755, true);
            }
            
            file_put_contents(
                $route,
                file_get_contents(__DIR__ .'/../stubs/make/routes/'. $stub),
                FILE_APPEND
            );
        }
    }
    
    /**
     * Compiles the controllers.
     *
     * @return string
     */
    protected function compileControllerStub($stub)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__ .'/../stubs/make/controllers/'. $stub)
        );
    }
    
    /**
     * Compiles the notification stub.
     * 
     * @return string
     */
    protected function compileNotificationStub($stub)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__ .'/../stubs/make/notifications/'. $stub)
        );
    }
}