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
    protected $description = 'Scaffold basic confirmation views, routes and controllers';
    
    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        __DIR__ .'/../stubs/make/views/send.stub' => 
            'resources/views/auth/emails/send.blade.php',
        __DIR__ .'/../stubs/make/views/confirm.stub' => 
            'resources/views/auth/emails/confirm.blade.php'
    ];
    
    /**
     * The controllers that need to be exported
     * 
     * @var array
     */
    protected $controllers = [
        __DIR__ .'/../stubs/make/controllers/SendEmailConfirmationController.stub' => 
            'App/Http/Controllers/Auth/SendEmailConfirmationController.php',
        __DIR__ .'/../stubs/make/controllers/ConfirmEmailController.stub' => 
            'App/Http/Controllers/Auth/ConfirmEmailController.php'
    ];
    
    /**
     * The notifications that need to be exported
     * 
     * @var array
     */
    protected $notifications = [
        __DIR__ .'/../stubs/make/notifications/Confirmation.stub' => 
            'App/Notifications/Auth/Confirmation.php'
    ];
    
    /**
     * The routes that need to be exported
     * 
     * @var array 
     */
    protected $routes = [
        __DIR__ .'/../stubs/make/routes.stub' => 'routes/web.php'
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
        $this->checkDirs($this->views);
        $this->checkDirs($this->controllers);
        $this->checkDirs($this->notifications);
        $this->checkDirs($this->routes);
    }
    
    /**
     * Check dirs
     * 
     * @param array $arr
     */
    protected function checkDirs(array $arr)
    {
        foreach ($arr as $file) {
            $this->makeDirIfNotExists(base_path($file));
        }
    }
    
    /**
     * Make a new dir if not exists
     * 
     * @param string $path
     */
    protected function makeDirIfNotExists($path)
    {
        $dir = dirname($path);
        
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    /**
     * Export the confirmation views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $stub => $dist) {
            $path = base_path($dist);
            
            if (file_exists($path) && ! $this->option('force')) {
                if (! $this->confirm("The [{$path}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy($stub, $path);
        }
    }
    
    /**
     * Export the confirmation controllers
     * 
     * @return void
     */
    protected function exportControllers()
    {   
        foreach ($this->controllers as $stub => $dist) {
            $path = base_path($dist);
            
            if (file_exists($path) && ! $this->option('force')) {
                if (! $this->confirm("The [{$path}] controller already exists. Do you want to replace it?")) {
                    continue;
                }
            }
            
            file_put_contents($path, $this->compileStub($stub));
        }
    }
    
    /**
     * Export the notifications notifications
     * 
     * @return void
     */
    protected function exportNotifications()
    {
        foreach ($this->notifications as $stub => $dist) {
            $path = base_path($dist);
        
            if (file_exists($path) && ! $this->option('force')) {
                if (! $this->confirm("The [{$path}] Notification already exists. Do you want to replace it?")) {
                    return;
                }
            }
            
            file_put_contents($path, $this->compileStub($stub));
        }
    }
    
    /**
     * Export confirmation routes
     * 
     * @return void 
     */
    protected function exportRoutes()
    {
        foreach ($this->routes as $stub => $dist) {
            $path = base_path($dist);
            $content = file_get_contents($stub);
            
            if (! $this->option('force') && 
                strpos(file_get_contents($path), $content) !== false) {
                if (! $this->confirm("The file [{$path}] already contains confirmation route. Do you want to replace it?")) {
                    continue;
                }
                
                file_put_contents(
                    $path, str_replace($content, '', file_get_contents($path))
                );
            }
            
            file_put_contents($path, $content, FILE_APPEND);
        }
    }
    
    /**
     * Compile stub.
     *
     * @return string
     */
    protected function compileStub($stub)
    {
        return str_replace('{{namespace}}', $this->getAppNamespace(),
            file_get_contents($stub)
        );
    }
}