<?php namespace App\Core\Providers;

use  AltThree\Bus\Dispatcher;
use App\Core\Console\TestCommand;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider {
    
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    
    /**
     * Boot the application events.
     *
     * @param Dispatcher $dispatcher
     */
    public function boot(Dispatcher $dispatcher) {
        $dispatcher->mapUsing(function ($command) {
            echo $command;
            
            $handler = str_replace('\\Commands\\', '\\Handlers\\', get_class($command));
            $handler .= 'Handler@handle';
            return $handler;
        });
        
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //
        $this->registerCommands();
    }
    
    /**
     * Register the console commands
     */
    private function registerCommands() {
        $this->commands([
            TestCommand::class,
        ]);
    }
    
    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig() {
        $this->publishes([__DIR__.'/../Config/config.php' => config_path('core.php'),]);
        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'core');
    }
    
    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews() {
        $viewPath = base_path('resources/views/modules/core');
        
        $sourcePath = __DIR__.'/../Resources/views';
        
        $this->publishes([$sourcePath => $viewPath]);
        
        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/core';
        }, \Config::get('view.paths')), [$sourcePath]), 'core');
    }
    
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations() {
        $langPath = base_path('resources/lang/modules/core');
        
        if(is_dir($langPath)){
            $this->loadTranslationsFrom($langPath, 'core');
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'core');
        }
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array();
    }
    
}
