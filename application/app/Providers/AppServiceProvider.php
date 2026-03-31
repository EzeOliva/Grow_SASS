<?php

namespace App\Providers;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url) {

        //[growcrm] disable debug bar in production mode
        if (!env('APP_DEBUG_TOOLBAR')) {
            \Debugbar::disable();
        }

        //[growcrm] force SSL rul's
        if (env('ENFORCE_SSL', false)) {
            $url->forceScheme('https');
        }

        //[growcrm] - use bootstrap css for paginator
        Paginator::useBootstrap();

        //[grocrm] - custom views directory - used by imap email for embedded imaged, but can also be used for any temp blade filed 
        //Usage - view('temp::somefile');
        View::addNamespace('temp', path_storage('temp'));

        //[growcrm]
        $this->app->bind(Carbon::class, function (Container $container) {
            return new Carbon('now', 'Europe/Brussels');
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        // Bind the tenant facade
        $this->app->bind('tenant', function ($app) {
            if ($app->bound('currentTenant')) {
                return $app->make('currentTenant');
            }
            
            // Return a default tenant if none is bound
            $defaultTenant = new \stdClass();
            $defaultTenant->id = 1;
            $defaultTenant->tenant_id = 1;
            $defaultTenant->database = env('TENANT_DB', 'growcrm_tenant_1');
            $defaultTenant->name = 'Default Tenant';
            
            return $defaultTenant;
        });
        
        // Bind the Spatie Tenant class to our custom implementation
        $this->app->bind(\Spatie\Multitenancy\Models\Tenant::class, function ($app) {
            return new \App\Models\CustomTenant();
        });
    }
}
