<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @fileoverview Tenant facade to handle type compatibility issues
 * @description Provides a facade interface for tenant operations
 */
class Tenant extends Facade
{
    /**
     * @description Get the facade accessor
     */
    protected static function getFacadeAccessor()
    {
        return 'tenant';
    }
    
    /**
     * @description Get the current tenant
     */
    public static function current()
    {
        if (app()->bound('currentTenant')) {
            return app('currentTenant');
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
        if (app()->bound('currentTenant')) {
            app()->forgetInstance('currentTenant');
        }
    }
}
