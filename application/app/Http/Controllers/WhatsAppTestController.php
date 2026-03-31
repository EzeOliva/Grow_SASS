<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppCloudApiService;
use App\Services\EnhancedEmailService;
use Illuminate\Support\Facades\Validator;

class WhatsAppTestController extends Controller
{
    /**
     * Test WhatsApp Cloud API connection with credentials from frontend
     */
    public function testWhatsAppConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
            'phone_number_id' => 'required|string',
            'business_account_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $whatsappService = new WhatsAppCloudApiService(
                $request->access_token,
                $request->phone_number_id,
                $request->business_account_id
            );

            $result = $whatsappService->testConnection();
            
            return response()->json([
                'success' => true,
                'whatsapp_test' => $result,
                'message' => 'WhatsApp Cloud API test completed'
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test sending WhatsApp message with credentials from frontend
     */
    public function testWhatsAppMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'message' => 'required|string|max:1000',
            'access_token' => 'required|string',
            'phone_number_id' => 'required|string',
            'business_account_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create WhatsApp service with credentials from frontend
            $whatsappService = new WhatsAppCloudApiService(
                $request->access_token,
                $request->phone_number_id,
                $request->business_account_id
            );

            $result = $whatsappService->sendTextMessage(
                $request->phone_number,
                $request->message
            );

            return response()->json([
                'success' => true,
                'whatsapp_result' => $result,
                'message' => 'WhatsApp message test completed'
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp message test failed', [
                'phone' => $request->phone_number,
                'message' => $request->message,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test SMTP connection with credentials from frontend
     */
    public function testSmtpConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'required|string|in:tls,ssl',
            'from_address' => 'required|email',
            'from_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create SMTP configuration from frontend
            $smtpConfig = [
                'host' => $request->smtp_host,
                'port' => $request->smtp_port,
                'username' => $request->smtp_username,
                'password' => $request->smtp_password,
                'encryption' => $request->smtp_encryption,
                'from_address' => $request->from_address,
                'from_name' => $request->from_name
            ];

            // Create email service with new configuration
            $emailService = new EnhancedEmailService($smtpConfig);

            $result = $emailService->testSmtpConnection();
            
            return response()->json([
                'success' => true,
                'smtp_test' => $result,
                'message' => 'SMTP connection test completed'
            ]);

        } catch (\Exception $e) {
            Log::error('SMTP test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test sending email with SMTP credentials from frontend
     */
    public function testEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'required|string|in:tls,ssl',
            'from_address' => 'required|email',
            'from_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create SMTP configuration from frontend
            $smtpConfig = [
                'host' => $request->smtp_host,
                'port' => $request->smtp_port,
                'username' => $request->smtp_username,
                'password' => $request->smtp_password,
                'encryption' => $request->smtp_encryption,
                'from_address' => $request->from_address,
                'from_name' => $request->from_name
            ];

            // Create email service with new configuration
            $emailService = new EnhancedEmailService($smtpConfig);

            $result = $emailService->sendTextEmail(
                $request->email,
                $request->subject,
                $request->message
            );

            return response()->json([
                'success' => true,
                'email_result' => $result,
                'message' => 'Email test completed'
            ]);

        } catch (\Exception $e) {
            Log::error('Email test failed', [
                'email' => $request->email,
                'subject' => $request->subject,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test sending WhatsApp ticket reply email with SMTP credentials from frontend
     */
    public function testWhatsAppTicketEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'ticket_id' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'contact_name' => 'required|string|max:255',
            'agent_name' => 'required|string|max:255',
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'required|string|in:tls,ssl',
            'from_address' => 'required|email',
            'from_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create SMTP configuration from frontend
            $smtpConfig = [
                'host' => $request->smtp_host,
                'port' => $request->smtp_port,
                'username' => $request->smtp_username,
                'password' => $request->smtp_password,
                'encryption' => $request->smtp_encryption,
                'from_address' => $request->from_address,
                'from_name' => $request->from_name
            ];

            $ticketData = [
                'contact_email' => $request->email,
                'ticket_id' => $request->ticket_id,
                'subject' => $request->subject,
                'message' => $request->message,
                'contact_name' => $request->contact_name,
                'agent_name' => $request->agent_name,
                'ticket_url' => 'https://example.com/tickets/' . $request->ticket_id
            ];

            // Create email service with new configuration
            $emailService = new EnhancedEmailService($smtpConfig);

            $result = $emailService->sendWhatsAppTicketReply($ticketData);

            return response()->json([
                'success' => true,
                'email_result' => $result,
                'message' => 'WhatsApp ticket reply email test completed'
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp ticket email test failed', [
                'ticket_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Comprehensive test of both services with credentials from frontend
     */
    public function runComprehensiveTest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'whatsapp_access_token' => 'required|string',
                'whatsapp_phone_number_id' => 'required|string',
                'whatsapp_business_account_id' => 'nullable|string',
                'smtp_host' => 'required|string',
                'smtp_port' => 'required|integer',
                'smtp_username' => 'required|string',
                'smtp_password' => 'required|string',
                'smtp_encryption' => 'required|string|in:tls,ssl',
                'from_address' => 'required|email',
                'from_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $results = [];

            // Create WhatsApp service with credentials from frontend
            $whatsappService = new WhatsAppCloudApiService(
                $request->whatsapp_access_token,
                $request->whatsapp_phone_number_id,
                $request->whatsapp_business_account_id
            );

            // Test WhatsApp Cloud API connection
            $results['whatsapp_connection'] = $whatsappService->testConnection();

            // Create SMTP configuration from frontend
            $smtpConfig = [
                'host' => $request->smtp_host,
                'port' => $request->smtp_port,
                'username' => $request->smtp_username,
                'password' => $request->smtp_password,
                'encryption' => $request->smtp_encryption,
                'from_address' => $request->from_address,
                'from_name' => $request->from_name
            ];

            // Create email service with new configuration
            $emailService = new EnhancedEmailService($smtpConfig);

            // Test SMTP connection
            $results['smtp_connection'] = $emailService->testSmtpConnection();

            // Test sending a test WhatsApp message
            $results['whatsapp_message'] = $whatsappService->sendTextMessage(
                '+1234567890', // Test phone number
                'This is a test message from WhatsApp Cloud API'
            );

            // Test sending a test email
            if ($results['smtp_connection']['success']) {
                $results['test_email'] = $emailService->sendTextEmail(
                    $request->from_address,
                    'Comprehensive Test Email',
                    'This is a test email from the enhanced email service'
                );
            } else {
                $results['test_email'] = [
                    'success' => false,
                    'error' => 'SMTP not configured properly'
                ];
            }

            return response()->json([
                'success' => true,
                'comprehensive_test_results' => $results,
                'message' => 'Comprehensive test completed'
            ]);

        } catch (\Exception $e) {
            Log::error('Comprehensive test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
