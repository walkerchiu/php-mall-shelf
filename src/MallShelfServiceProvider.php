<?php

namespace WalkerChiu\MallShelf;

use Illuminate\Support\ServiceProvider;

class MallShelfServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/mall-shelf.php' => config_path('wk-mall-shelf.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_mall_shelf_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_mall_shelf_table.php',
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-mall-shelf');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-mall-shelf'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-mall-shelf.command.cleaner')
            ]);
        }

        config('wk-core.class.mall-shelf.relation')::observe(config('wk-core.class.mall-shelf.relationObserver'));
        config('wk-core.class.mall-shelf.relationLang')::observe(config('wk-core.class.mall-shelf.relationLangObserver'));
        config('wk-core.class.mall-shelf.catalog')::observe(config('wk-core.class.mall-shelf.catalogObserver'));
        config('wk-core.class.mall-shelf.catalogLang')::observe(config('wk-core.class.mall-shelf.catalogLangObserver'));
        config('wk-core.class.mall-shelf.product')::observe(config('wk-core.class.mall-shelf.productObserver'));
        config('wk-core.class.mall-shelf.productLang')::observe(config('wk-core.class.mall-shelf.productLangObserver'));
        config('wk-core.class.mall-shelf.stock')::observe(config('wk-core.class.mall-shelf.stockObserver'));
        config('wk-core.class.mall-shelf.stockLang')::observe(config('wk-core.class.mall-shelf.stockLangObserver'));
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-mall-shelf')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/mall-shelf.php', 'wk-mall-shelf'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/mall-shelf.php', 'mall-shelf'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
