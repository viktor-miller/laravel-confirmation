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
                    {--views            : Only scaffold the confirmation views}
                    {--controllers      : Only scaffold the confirmation controllers}
                    {--notifications    : Only scaffold the confirmation notifications}
                    {--routes           : Only scaffold the confirmation routes}
                    {--force            : Overwrite existing views by default}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic confirmation views and routes';
    
    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'auth/confirmation.stub' => 'auth/confirmation.blade.php'
    ];
    
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();
        
        if ($this->option('notifications')) {
            $this->exportNotifications();
        } elseif ($this->option('controllers')) {
            $this->exportControllers();
        } elseif ($this->option('views')) {
            $this->exportViews();
        } elseif ($this->option('routes')) {
            $this->exportRoutes();
        } else {
            $this->exportViews();
            $this->exportControllers();
            $this->exportNotifications();
            $this->exportRoutes();
        }

        $this->info('Confirmation scaffolding generated successfully.');
    }
    
    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir($directory = resource_path('views/auth'))) {
            mkdir($directory, 0755, true);
        }
        
        if (! is_dir($directory = app_path('Notifications/Auth'))) {
            mkdir($directory, 0755, true);
        }
    }
    
    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = resource_path('views/'. $value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__.'/../stubs/make/views/'. $key,
                $view
            );
        }
    }
    
    protected function exportControllers()
    {
        $path = 'Http/Controllers/Auth/ConfirmationController.php';
        
        if (file_exists($file = app_path($path)) && ! $this->option('force')) {
            if (! $this->confirm("The [{$path}] Controller already exists. Do you want to replace it?")) {
                return;
            }
        }
            
        file_put_contents(
            $file,
            $this->compileControllerStub()
        );
    }
    
    protected function exportNotifications()
    {
        $path = 'Notifications/Confirmation.php';
        
        if (file_exists($file = app_path($path)) && ! $this->option('force')) {
            if (! $this->confirm("The [{$path}] Notification already exists. Do you want to replace it?")) {
                return;
            }
        }
            
        file_put_contents(
            $file,
            $this->compileNotificationStub()
        );
    }
    
    protected function exportRoutes()
    {
        $path = 'routes/web.php';
        
        if (file_exists($file = base_path($path)) && 
            ! $this->option('force') && 
              strpos(file_get_contents(base_path($path)), 'confirmation') !== FALSE
        ) {
            if (! $this->confirm("The file [{$path}] already contains confirmation config. Do you want to replace it?")) {
                return;
            }
        }
        
        file_put_contents(
            $file,
            file_get_contents(__DIR__ .'/../stubs/make/routes.stub'),
            FILE_APPEND
        );
    }
    
    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__ .'/../stubs/make/controllers/ConfirmationController.stub')
        );
    }
    
    /**
     * Compiles the ConfirmationNotification stub.
     * 
     * @return string
     */
    protected function compileNotificationStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__ .'/../stubs/make/notifications/Confirmation.stub')
        );
    }
}