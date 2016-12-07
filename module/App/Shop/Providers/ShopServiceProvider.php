<?php namespace App\Shop\Providers;

use Illuminate\Support\ServiceProvider;

class ShopServiceProvider extends ServiceProvider {
    
    protected $moduleName;
    protected $modulePath;
    
    public function __construct(\Illuminate\Contracts\Foundation\Application $app) {
        parent::__construct($app);
        $this->moduleName = strtolower(preg_replace('/(.+)ServiceProvider/i', '$1', array_pop(explode('\\', get_class($this)))));
        $this->modulePath = \Module::get($this->moduleName)->getPath();
    }
    
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->publishStatics();
    }
    
    public function path($path = null) {
        return $path ? $this->modulePath.'/'.$path : $this->modulePath;
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //

    }
    
    protected function publishStatics() {
        $dataPath = public_path('data');
        
        $sourcePath = $this->path('data');
        
        $this->publishes([
            $sourcePath => $dataPath
        ]);
        
    }
    
    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig() {
        $this->publishes([
            $this->path('config/config.php') => config_path('app/shop.php'),
        ]);
        $this->mergeConfigFrom(
            $this->path('config/config.php'), 'app/shop'
        );
    }
    
    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews() {
        $viewPath = base_path('resources/views/modules/shop');
        
        $sourcePath = $this->path('resources/views');
        
        $this->publishes([
            $sourcePath => $viewPath
        ]);
        
        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/shop';
        }, \Config::get('view.paths')), [$sourcePath]), 'shop');
    }
    
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations() {
        $langPath = base_path('resources/lang/modules/shop');
        
        if(is_dir($langPath)){
            $this->loadTranslationsFrom($langPath, 'shop');
        } else {
            $this->loadTranslationsFrom($this->path('resources/lang'), 'shop');
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
