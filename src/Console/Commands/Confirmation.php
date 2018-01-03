<?php

namespace ViktorMiller\LaravelConfirmation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;

class Confirmation extends Command
{
    use DetectsApplicationNamespace;
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'confirmation
                    {--views : Only scaffold the confirmation views}
                    {--force : Overwrite existing views by default}';
    
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

        $this->exportViews();

        if (! $this->option('views')) {
            file_put_contents(
                app_path('Http/Controllers/Auth/ConfirmationController.php'),
                $this->compileControllerStub()
            );

            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__. '/../stubs/make/routes.stub'),
                FILE_APPEND
            );
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
}