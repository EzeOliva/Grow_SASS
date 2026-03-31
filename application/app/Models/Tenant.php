<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @fileoverview Simple Tenant model to replace missing Spatie Multitenancy package
 * @description Provides basic tenant functionality and current() method
 */
class Tenant extends Model
{
    protected $connection = 'landlord';
    protected $table = 'tenants';
    
    protected $fillable = [
        'tenant_id',
        'database',
        'name',
        'domain',
        'status'
    ];
    
    /**
     * @description Get the current tenant from the application container
     */
    public static function current()
    {
        if (app()->bound('currentTenant')) {
            $boundTenant = app('currentTenant');
            // If the bound tenant is our Tenant model, return it
            if ($boundTenant instanceof self) {
                return $boundTenant;
            }
            // If it's something else, wrap it in our model
            $tenant = new self();
            $tenant->id = $boundTenant->id ?? 1;
            $tenant->tenant_id = $boundTenant->tenant_id ?? 1;
            $tenant->database = $boundTenant->database ?? env('TENANT_DB', 'growcrm_tenant_1');
            $tenant->name = $boundTenant->name ?? 'Default Tenant';
            return $tenant;
        }
        
        // Return a default tenant if none is bound
        $defaultTenant = new self();
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
        // Since we don't have a tenants table, return a default tenant
        $defaultTenant = new self();
        $defaultTenant->id = 1;
        $defaultTenant->tenant_id = 1;
        $defaultTenant->database = env('TENANT_DB', 'growcrm_tenant_1');
        $defaultTenant->name = 'Default Tenant';
        
        return $defaultTenant;
    }
}

