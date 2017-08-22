<?php

namespace JimHlad\LeapFrog;

use Illuminate\Support\Facades\Blade;
use Yab\FormMaker\Services\FormMaker;
use Illuminate\Foundation\AliasLoader;
use Yab\FormMaker\Services\InputMaker;
use Illuminate\Support\ServiceProvider;

class LeapFrogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Publishes/Config/leapfrog.php' => config_path('leapfrog.php'),
        ]);

        $this->publishes([
            __DIR__.'/Publishes/Routes/leapfrog.php' => base_path('routes/leapfrog.php'),
        ]);

        $this->publishes([
            __DIR__.'/Publishes/Services/BaseService.php' => app_path('Services/BaseService.php'),
        ]);

        $this->publishes([
            __DIR__.'/Publishes/Assets' => public_path('jimhlad/leapfrog'),
        ], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('JimHlad\LeapFrog\Controllers\DashboardController');
        $this->app->make('JimHlad\LeapFrog\Controllers\CrudController');

        $this->registerFormMaker();
        $this->registerMigrationGenerator();

        $this->loadViewsFrom(__DIR__.'/Views', 'leapfrog');
    }

    /**
     * Register the FormMaker dependency
     * 
     * @return void
     */
    public function registerFormMaker()
    {
        /*
        |--------------------------------------------------------------------------
        | Providers
        |--------------------------------------------------------------------------
        */
       
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);

        /*
        |--------------------------------------------------------------------------
        | Register the Utilities
        |--------------------------------------------------------------------------
        */
       
        $this->app->singleton('FormMaker', function () {
            return new FormMaker();
        });
        $this->app->singleton('InputMaker', function () {
            return new InputMaker();
        });
        $loader = AliasLoader::getInstance();
        $loader->alias('FormMaker', \Yab\FormMaker\Facades\FormMaker::class);
        $loader->alias('InputMaker', \Yab\FormMaker\Facades\InputMaker::class);
        // Thrid party
        $loader->alias('Form', \Collective\Html\FormFacade::class);
        $loader->alias('HTML', \Collective\Html\HtmlFacade::class);

        /*
        |--------------------------------------------------------------------------
        | Blade Directives
        |--------------------------------------------------------------------------
        */
       
        // Form Maker
        Blade::directive('form_maker_table', function ($expression) {
            return "<?php echo FormMaker::fromTable($expression); ?>";
        });
        Blade::directive('form_maker_array', function ($expression) {
            return "<?php echo FormMaker::fromArray($expression); ?>";
        });
        Blade::directive('form_maker_object', function ($expression) {
            return "<?php echo FormMaker::fromObject($expression); ?>";
        });
        Blade::directive('form_maker_columns', function ($expression) {
            return "<?php echo FormMaker::getTableColumns($expression); ?>";
        });
        // Label Maker
        Blade::directive('input_maker_label', function ($expression) {
            return "<?php echo InputMaker::label($expression); ?>";
        });
        Blade::directive('input_maker_create', function ($expression) {
            return "<?php echo InputMaker::create($expression); ?>";
        });
    }

    /**
     * Register the migration generator dependency
     * 
     * @return void
     */
    public function registerMigrationGenerator()
    {
        $this->app->register(\Laracasts\Generators\GeneratorsServiceProvider::class);
    }
}
