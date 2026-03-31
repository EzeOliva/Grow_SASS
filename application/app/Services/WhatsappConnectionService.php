<?php

namespace App\Services;

use App\Models\WhatsappConnection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * @fileoverview WhatsApp Connection Service - Manages WhatsApp connection operations
 * @description Handles connection creation, QR code generation, status management, and connection testing
 */
class WhatsappConnectionService
{
    /**
     * @description Create a new WhatsApp connection
     */
    public function createConnection(array $data): WhatsappConnection
    {
        try {
            $data['tenant_id'] = app('currentTenant')->id ?? 1;
            $data['status'] = 'disconnected';
            $data['connection_type'] = $data['connection_type'] ?? 'whatsapp_business';
            
            return WhatsappConnection::create($data);
        } catch (\Exception $e) {
            Log::error('Error creating WhatsApp connection: ' . $e->getMessage());
            throw new \Exception('Failed to create connection: ' . $e->getMessage());
        }
    }

    /**
     * @description Update connection status
     */
    public function updateConnectionStatus(WhatsappConnection $connection, string $status): bool
    {
        try {
            $connection->update([
                'status' => $status,
                'last_status_update' => now()
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating connection status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @description Generate QR code for connection
     */
    public function generateQRCode(WhatsappConnection $connection): ?string
    {
        try {
            // This would integrate with actual WhatsApp Business API
            // For now, we'll generate a placeholder QR code
            $qrData = [
                'connection_id' => $connection->id,
                'phone_number' => $connection->phone_number,
                'timestamp' => now()->timestamp
            ];
            
            $qrCode = base64_encode(json_encode($qrData));
            
            // Store QR code data
            $connection->update([
                'qr_code_data' => $qrCode,
                'qr_code_generated_at' => now()
            ]);
            
            return $qrCode;
        } catch (\Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @description Test connection functionality
     */
    public function testConnection(WhatsappConnection $connection): array
    {
        try {
            // Simulate connection test
            $testResults = [
                'connection_id' => $connection->id,
                'phone_number' => $connection->phone_number,
                'status' => 'testing',
                'test_timestamp' => now(),
                'results' => []
            ];
            
            // Test phone number format
            $testResults['results']['phone_format'] = $this->validatePhoneNumber($connection->phone_number);
            
            // Test API credentials (if applicable)
            if ($connection->api_key) {
                $testResults['results']['api_credentials'] = $this->testAPICredentials($connection);
            }
            
            // Test webhook URL (if applicable)
            if ($connection->webhook_url) {
                $testResults['results']['webhook'] = $this->testWebhook($connection->webhook_url);
            }
            
            $testResults['overall_status'] = $this->calculateOverallTestStatus($testResults['results']);
            
            // Update connection with test results
            $connection->update([
                'last_test_at' => now(),
                'test_results' => json_encode($testResults)
            ]);
            
            return $testResults;
        } catch (\Exception $e) {
            Log::error('Error testing connection: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'overall_status' => 'failed'
            ];
        }
    }

    /**
     * @description Validate phone number format
     */
    private function validatePhoneNumber(string $phoneNumber): array
    {
        $phone = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        if (preg_match('/^\+[1-9]\d{1,14}$/', $phone)) {
            return ['valid' => true, 'message' => 'Valid international format'];
        }
        
        if (preg_match('/^[1-9]\d{9,14}$/', $phone)) {
            return ['valid' => true, 'message' => 'Valid national format'];
        }
        
        return ['valid' => false, 'message' => 'Invalid phone number format'];
    }

    /**
     * @description Test API credentials
     */
    private function testAPICredentials(WhatsappConnection $connection): array
    {
        // This would make an actual API call to test credentials
        // For now, we'll simulate the test
        return [
            'valid' => true,
            'message' => 'API credentials appear valid',
            'test_type' => 'simulated'
        ];
    }

    /**
     * @description Test webhook URL
     */
    private function testWebhook(string $webhookUrl): array
    {
        try {
            $response = \Http::timeout(10)->post($webhookUrl, [
                'test' => true,
                'timestamp' => now()->timestamp
            ]);
            
            if ($response->successful()) {
                return ['valid' => true, 'message' => 'Webhook responded successfully'];
            }
            
            return ['valid' => false, 'message' => 'Webhook responded with status: ' . $response->status()];
        } catch (\Exception $e) {
            return ['valid' => false, 'message' => 'Webhook test failed: ' . $e->getMessage()];
        }
    }

    /**
     * @description Calculate overall test status
     */
    private function calculateOverallTestStatus(array $results): string
    {
        $validTests = 0;
        $totalTests = count($results);
        
        foreach ($results as $test) {
            if (isset($test['valid']) && $test['valid']) {
                $validTests++;
            }
        }
        
        if ($validTests === $totalTests) {
            return 'passed';
        } elseif ($validTests > 0) {
            return 'partial';
        } else {
            return 'failed';
        }
    }

    /**
     * @description Get connection statistics
     */
    public function getConnectionStats(int $connectionId): array
    {
        try {
            $connection = WhatsappConnection::findOrFail($connectionId);
            
            return [
                'connection_id' => $connection->id,
                'status' => $connection->status,
                'uptime' => $this->calculateUptime($connection),
                'messages_sent' => $this->getMessageCount($connectionId, 'sent'),
                'messages_received' => $this->getMessageCount($connectionId, 'received'),
                'last_activity' => $connection->last_activity_at,
                'created_at' => $connection->created_at
            ];
        } catch (\Exception $e) {
            Log::error('Error getting connection stats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @description Calculate connection uptime
     */
    private function calculateUptime(WhatsappConnection $connection): string
    {
        if (!$connection->connected_at) {
            return '0 minutes';
        }
        
        $uptime = now()->diffInMinutes($connection->connected_at);
        
        if ($uptime < 60) {
            return $uptime . ' minutes';
        } elseif ($uptime < 1440) {
            return round($uptime / 60, 1) . ' hours';
        } else {
            return round($uptime / 1440, 1) . ' days';
        }
    }

    /**
     * @description Get message count for connection
     */
    private function getMessageCount(int $connectionId, string $direction): int
    {
        try {
            // This would query the actual messages table
            // For now, return a placeholder
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
