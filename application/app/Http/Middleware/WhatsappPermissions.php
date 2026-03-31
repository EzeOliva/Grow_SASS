<?php

/**
 * @fileoverview WhatsApp Permissions Middleware
 * @description Handles WhatsApp access permissions based on user roles
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WhatsappPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = 'access')
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to WhatsApp features');
        }

        $user = auth()->user();
        $roleId = $user->role_id;

        // Define role-based permissions
        $permissions = $this->getRolePermissions($roleId);

        // Check specific permission
        if (!$this->hasPermission($permissions, $permission)) {
            abort(403, 'You do not have permission to access this WhatsApp feature');
        }

        return $next($request);
    }

    /**
     * Get permissions for a specific role
     *
     * @param int $roleId
     * @return array
     */
    private function getRolePermissions($roleId)
    {
        switch ($roleId) {
            case 1: // Administrator
                return [
                    'access' => true,
                    'manage_messages' => true,
                    'assign_tickets' => true,
                    'reply_clients' => true,
                    'view_only' => false
                ];
            
            case 3: // Staff (Manager/Agent - based on context)
                // Check if user has manager-level permissions
                if ($this->isManager($roleId)) {
                    return [
                        'access' => true,
                        'manage_messages' => true,
                        'assign_tickets' => true,
                        'reply_clients' => true,
                        'view_only' => false
                    ];
                } else {
                    // Agent permissions
                    return [
                        'access' => true,
                        'manage_messages' => false,
                        'assign_tickets' => false,
                        'reply_clients' => true,
                        'view_only' => false
                    ];
                }
            
            case 2: // Client (Viewer)
            default:
                return [
                    'access' => false,
                    'manage_messages' => false,
                    'assign_tickets' => false,
                    'reply_clients' => false,
                    'view_only' => true
                ];
        }
    }

    /**
     * Check if user has a specific permission
     *
     * @param array $permissions
     * @param string $permission
     * @return bool
     */
    private function hasPermission($permissions, $permission)
    {
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }

    /**
     * Determine if user is a manager (this could be enhanced with additional logic)
     *
     * @param int $roleId
     * @return bool
     */
    private function isManager($roleId)
    {
        // For now, we'll treat Staff role (3) as Agent
        // In a real implementation, you might have separate Manager and Agent roles
        // or determine this based on additional user attributes
        return false; // Staff role is treated as Agent
    }
}
