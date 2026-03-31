<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

/**
 * @fileoverview Tenant service to handle tenant operations
 * @description Provides tenant functionality without depending on Spatie package
 */
class TenantService
{
    /**
     * @description Get the current tenant
     */
    public static function current()
    {
        if (App::bound('currentTenant')) {
            return App::make('currentTenant');
        }
        
        // Return a default tenant if none is bound
        $defaultTenant = new \stdClass();
        $defaultTenant->id = 1;
        $defaultTenant->tenant_id = 1;
        $defaultTenant->database = env('TENANT_DB', 'growcrm_tenant_1');
        $defaultTenant->name = 'Default Tenant';
        
        return $defaultTenant;
    }
    
    /**
     * @description Get the first available tenant
     */
    public static function first()
    {
        return self::current();
    }
    
    /**
     * @description Forget the current tenant
     */
    public static function forgetCurrent()
    {
        if (App::bound('currentTenant')) {
            App::forgetInstance('currentTenant');
        }
    }
}
