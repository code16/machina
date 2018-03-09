<?php 

namespace Code16\Machina;


use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTManager;
use Illuminate\Support\ServiceProvider;
use Code16\Machina\Commands\KeyCommand;
use Code16\Machina\Adapters\JwtUserAdapter;
use Code16\Machina\Repositories\ClientRepositoryInterface;

class MachinaServiceProvider extends ServiceProvider {

    /**
     * Pachage identifier
     * 
     * @var  string
     */
    protected $packageName = 'machina';

    /**
     * A list of artisan commands for your package
     * 
     * @var array
     */
    protected $commands = [
        KeyCommand::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerAuthProviders();

        $this->mapRoutes(
            $this->app->make('config')->get('machina.route-prefix')
        );

        // Regiter migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publish your config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path($this->packageName.'.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

    }

    /**
     * Map routes for login / refresh
     * @param  string $prefix
     * @return void
     */
    protected function mapRoutes(string $prefix)
    {
        $this->app->make('router')->prefix($prefix)
             ->namespace("Code16\Machina\Controllers")
             ->group(__DIR__.'/../routes/routes.php');
    }

    /**
     * Register UserProvider & Guard
     * 
     * @return void
     */
    protected function registerAuthProviders()
    {
        $auth = $this->app->make('auth');
        $auth->extend('machina', function ($app, $name, array $config) use($auth){
            return new MachinaGuard(
                $app->make(JWTManager::class),
                $app->make(ClientRepositoryInterface::class)
            );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {   
        $this->app->register(\Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class);

        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', $this->packageName
        );
    }

}
