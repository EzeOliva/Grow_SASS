<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudApiService
{
    protected $accessToken;
    protected $phoneNumberId;
    protected $businessAccountId;
    protected $version = 'v18.0';
    protected $baseUrl = 'https://graph.facebook.com';

    public function __construct($accessToken = null, $phoneNumberId = null, $businessAccountId = null)
    {
        $this->accessToken = $accessToken;
        $this->phoneNumberId = $phoneNumberId;
        $this->businessAccountId = $businessAccountId;
    }

    /**
     * Send a text message via WhatsApp Cloud API
     */
    public function sendTextMessage($to, $message, $previewUrl = false)
    {
        try {
            if (!$this->accessToken || !$this->phoneNumberId) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp credentials not provided'
                ];
            }

            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";
            
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $this->formatPhoneNumber($to),
                'type' => 'text',
                'text' => [
                    'body' => $message,
                    'preview_url' => $previewUrl
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('WhatsApp Cloud API message sent successfully', [
                    'to' => $to,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                    'response' => $responseData
                ];
            } else {
                Log::error('WhatsApp Cloud API error', [
                    'to' => $to,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $response->body(),
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API exception', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send a media message (image, document, audio, video)
     */
    public function sendMediaMessage($to, $mediaUrl, $mediaType, $caption = null)
    {
        try {
            if (!$this->accessToken || !$this->phoneNumberId) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp credentials not provided'
                ];
            }

            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";
            
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $this->formatPhoneNumber($to),
                'type' => $mediaType,
                $mediaType => [
                    'link' => $mediaUrl
                ]
            ];

            if ($caption && in_array($mediaType, ['image', 'video', 'document'])) {
                $data[$mediaType]['caption'] = $caption;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('WhatsApp Cloud API media message sent successfully', [
                    'to' => $to,
                    'media_type' => $mediaType,
                    'message_id' => $responseData['messages'][0]['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                    'response' => $responseData
                ];
            } else {
                Log::error('WhatsApp Cloud API media message error', [
                    'to' => $to,
                    'media_type' => $mediaType,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $response->body(),
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API media message exception', [
                'to' => $to,
                'media_type' => $mediaType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send a template message
     */
    public function sendTemplateMessage($to, $templateName, $language = 'en_US', $components = [])
    {
        try {
            if (!$this->accessToken || !$this->phoneNumberId) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp credentials not provided'
                ];
            }

            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";
            
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $this->formatPhoneNumber($to),
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $language
                    ]
                ]
            ];

            if (!empty($components)) {
                $data['template']['components'] = $components;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('WhatsApp Cloud API template message sent successfully', [
                    'to' => $to,
                    'template' => $templateName,
                    'message_id' => $responseData['messages'][0]['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                    'response' => $responseData
                ];
            } else {
                Log::error('WhatsApp Cloud API template message error', [
                    'to' => $to,
                    'template' => $templateName,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $response->body(),
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API template message exception', [
                'to' => $to,
                'template' => $templateName,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get message status
     */
    public function getMessageStatus($messageId)
    {
        try {
            if (!$this->accessToken) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp credentials not provided'
                ];
            }

            $url = "{$this->baseUrl}/{$this->version}/{$messageId}";
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}"
            ])->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body(),
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API get message status exception', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get business account information
     */
    public function getBusinessAccountInfo()
    {
        try {
            if (!$this->accessToken || !$this->businessAccountId) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp credentials not provided'
                ];
            }

            $url = "{$this->baseUrl}/{$this->version}/{$this->businessAccountId}";
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}"
            ])->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body(),
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API get business account info exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get phone number information
     */
    public function getPhoneNumberInfo()
    {
        try {
            if (!$this->accessToken || !$this->phoneNumberId) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp credentials not provided'
                ];
            }

            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}";
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}"
            ])->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body(),
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API get phone number info exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number for WhatsApp API
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add country code if not present (assuming +1 for US, adjust as needed)
        if (!str_starts_with($cleanPhone, '1') && strlen($cleanPhone) === 10) {
            $cleanPhone = '1' . $cleanPhone;
        }
        
        // Add + prefix
        return '+' . $cleanPhone;
    }

    /**
     * Test the API connection
     */
    public function testConnection()
    {
        try {
            if (!$this->accessToken || !$this->phoneNumberId) {
                return [
                    'success' => false,
                    'message' => 'Connection failed - credentials not provided',
                    'error' => 'WhatsApp credentials not provided'
                ];
            }

            $result = $this->getPhoneNumberInfo();
            
            if ($result['success']) {
                Log::info('WhatsApp Cloud API connection test successful');
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'phone_number_info' => $result['data']
                ];
            } else {
                Log::error('WhatsApp Cloud API connection test failed', [
                    'error' => $result['error']
                ]);
                return [
                    'success' => false,
                    'message' => 'Connection failed',
                    'error' => $result['error']
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API connection test exception', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Connection test exception',
                'error' => $e->getMessage()
            ];
        }
    }
}
