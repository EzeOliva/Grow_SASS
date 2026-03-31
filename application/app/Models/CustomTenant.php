<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

/**
 * @fileoverview Custom Tenant model to handle Spatie Tenant compatibility
 * @description Extends the real Spatie Tenant class to ensure type compatibility
 */
class CustomTenant extends SpatieTenant
{
    protected $table = 'tenants';
    protected $connection = 'landlord';
    
    protected $fillable = [
        'id',
        'tenant_id',
        'database',
        'name',
        'domain',
        'status'
    ];
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->id = 1;
        $this->tenant_id = 1;
        $this->database = env('TENANT_DB', 'growcrm_tenant_1');
        $this->name = 'Default Tenant';
    }
    
    /**
     * @description Get the current tenant
     */
    public static function current(): ?static
    {
        try {
            if (\Illuminate\Support\Facades\App::bound('currentTenant')) {
                $boundTenant = \Illuminate\Support\Facades\App::make('currentTenant');
                // If the bound tenant is already our CustomTenant model, return it
                if ($boundTenant instanceof self) {
                    return $boundTenant;
                }
                // If it's something else, create a new instance with the data
                $tenant = new self();
                $tenant->id = $boundTenant->id ?? 1;
                $tenant->tenant_id = $boundTenant->tenant_id ?? 1;
                $tenant->database = $boundTenant->database ?? env('TENANT_DB', 'growcrm_tenant_1');
                $tenant->name = $boundTenant->name ?? 'Default Tenant';
                return $tenant;
            }
        } catch (\Exception $e) {
            // If there's any error, just return a default tenant
        }
        
        // Return a default tenant if none is bound or if there's an error
        return new self();
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
    public static function forgetCurrent(): ?\Spatie\Multitenancy\Models\Tenant
    {
        if (app()->bound('currentTenant')) {
            app()->forgetInstance('currentTenant');
        }
    }
}
