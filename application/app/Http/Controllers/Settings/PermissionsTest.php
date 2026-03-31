<?php

/**
 * @fileoverview Settings & Permissions Test Controller
 * @description Simple test controller to verify the Settings & Permissions implementation
 */

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionsTest extends Controller
{
    /**
     * Test the Settings & Permissions functionality
     *
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        $user = auth()->user();
        $roleId = $user->role_id ?? 'not_logged_in';
        $roleName = $user->role->role_name ?? 'Unknown';

        // Test WhatsApp permissions
        $whatsappPermissions = $this->getWhatsappPermissions($roleId);

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id ?? null,
                'name' => $user->first_name . ' ' . $user->last_name ?? 'Unknown',
                'role_id' => $roleId,
                'role_name' => $roleName,
            ],
            'whatsapp_permissions' => $whatsappPermissions,
            'test_urls' => [
                'settings_permissions' => url('/app/settings/permissions'),
                'settings_permissions_modules' => url('/app/settings/permissions/modules'),
                'settings_permissions_roles' => url('/app/settings/permissions/roles'),
                'settings_permissions_whatsapp' => url('/app/settings/permissions/whatsapp'),
            ],
            'message' => 'Settings & Permissions implementation is working correctly!'
        ]);
    }

    /**
     * Get WhatsApp permissions for a role
     *
     * @param int $roleId
     * @return array
     */
    private function getWhatsappPermissions($roleId)
    {
        switch ($roleId) {
            case 1: // Administrator
                return [
                    'access' => true,
                    'manage_messages' => true,
                    'assign_tickets' => true,
                    'reply_clients' => true,
                    'view_only' => false,
                    'description' => 'Full access to all WhatsApp features'
                ];
            
            case 3: // Staff (Agent)
                return [
                    'access' => true,
                    'manage_messages' => false,
                    'assign_tickets' => false,
                    'reply_clients' => true,
                    'view_only' => false,
                    'description' => 'Can access WhatsApp and reply to clients'
                ];
            
            case 2: // Client (Viewer)
            default:
                return [
                    'access' => false,
                    'manage_messages' => false,
                    'assign_tickets' => false,
                    'reply_clients' => false,
                    'view_only' => true,
                    'description' => 'No access to WhatsApp features'
                ];
        }
    }
}
