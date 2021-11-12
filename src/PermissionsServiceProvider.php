<?php

namespace Schoutentech\permissions;

use Illuminate\Support\ServiceProvider;
use Schoutentech\Permissions\Console\AddPremission;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Schoutentech\Permissions\Permissions;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
                Blade::directive('test', function () {
            return "<?php echo 'Hello world';?>";
        });
        // defining blade directives
        Blade::directive("guard", function($expression){
            $condition = "false";

            if(Auth::check()){

                $condition = json_encode(Permissions::checkSanctum($expression));
            }
            return "<?php if ($condition) { ?>";
        });
        Blade::directive("endguard", function(){
            return "<?php } ?>";
        });





            // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->commands([
                AddPremission::class,
            ]);
        }
        include __DIR__.'/routes.php';
        $this->loadMigrationsFrom(__DIR__."/database/migrations");

    }

}
