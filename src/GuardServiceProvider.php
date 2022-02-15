<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Permissions;

class GuardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Artisan::command('permissions:add {permission}', function(){
            $file = fopen(dirname(dirname(__DIR__)) . "/config/permissions", "a+") or $this->error("CANT FIND PERMISSIONS FILE IN CONFIG");
            fwrite($file, ($this->argument("permission") . "\n"));
            $this->info("Permission Added to permissions");
            fclose($file);
        });
        Blade::directive("viewOnly", function(){
            return "<?php if (\App\Permissions::checkSanctum('view_only')){?> disabled <?php
            }?>";
        });
        Blade::directive("autherise", function($expression){
            return "<?php if (\App\Permissions::checkSanctum('$expression')) {\Redirect::to(''/unautherised')->send();} ?>";
        });
        Blade::directive("useGuard", function(){
            return "<?php use Illuminate\Support\Facades\Auth; '?>";
        });
        Blade::directive("guard", function($expression){
            return "<?php if (\App\Permissions::checkSanctum('$expression')) { ?>";
        });
        Blade::directive("notGuard", function($expression){
            return "<?php if (! \App\Permissions::checkSanctum('$expression')) { ?>";
        });
        Blade::directive("anyGuard", function($expression){
            return "<?php if (\App\Permissions::anyCheck('$expression')) {?>";
        });
        Blade::directive("multiGuard", function($expression){
            return "<?php if (\App\Permissions::multiCheck('$expression')) { ?>";
        });
        Blade::directive("elseguard", function(){
            return "<?php } else { ?>";
        });
        Blade::directive("endguard", function(){
            return "<?php } ?>";
        });

    }
}
