<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * @fileoverview Eloquent trait to scope models by tenant and auto-fill tenant_id
 * @description Adds a global scope filtering by current tenant and sets tenant_id on create
 */
trait BelongsToTenant
{
    /**
     * @description Boot the tenant behavior for the model
     */
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model): void {
            if (empty($model->tenant_id)) {
                $model->tenant_id = app('currentTenant')->id ?? 1;
            }
        });

        // Temporarily disabled global scope to debug the issue
        // static::addGlobalScope('tenant', function (Builder $builder): void {
        //     try {
        //         $tenantId = app('currentTenant')->id ?? 1;
        //         if ($tenantId) {
        //             $table = $builder->getModel()->getTable();
        //             $builder->where($table . '.tenant_id', $tenant_id);
        //         }
        //     } catch (\Throwable $e) {
        //         // Fallback to tenant_id = 1 if currentTenant is not available
        //         $table = $builder->getModel()->getTable();
        //         $builder->where($table . '.tenant_id', 1);
        //     }
        // });
    }
}


