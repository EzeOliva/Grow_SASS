<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\Message;

class EnhancedEmailService
{
    protected $config;
    protected $defaultFrom;

    public function __construct($smtpConfig = null)
    {
        if ($smtpConfig) {
            // Use provided SMTP configuration
            $this->config = $smtpConfig;
            $this->defaultFrom = [
                'address' => $smtpConfig['from_address'],
                'name' => $smtpConfig['from_name']
            ];
        } else {
            // Use default configuration from config files
            $this->config = config('mail.mailers.smtp');
            $this->defaultFrom = config('mail.from');
        }
    }

    /**
     * Send a simple text email
     */
    public function sendTextEmail($to, $subject, $message, $from = null, $attachments = [])
    {
        try {
            $from = $from ?? $this->defaultFrom;

            // Configure mailer with custom SMTP settings if provided
            if ($this->config && isset($this->config['host'])) {
                $this->configureCustomMailer();
            }

            Mail::raw($message, function (Message $message) use ($to, $subject, $from, $attachments) {
                $message->from($from['address'], $from['name'])
                        ->to($to)
                        ->subject($subject);

                // Add attachments if any
                foreach ($attachments as $attachment) {
                    if (isset($attachment['path']) && file_exists($attachment['path'])) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'] ?? basename($attachment['path']),
                            'mime' => $attachment['mime'] ?? null
                        ]);
                    }
                }
            });

            Log::info('Text email sent successfully', [
                'to' => $to,
                'subject' => $subject,
                'from' => $from
            ]);

            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send text email', [
                'to' => $to,
                'subject' => $subject,
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
     * Configure custom mailer with provided SMTP settings
     */
    protected function configureCustomMailer()
    {
        try {
            // Create a new mailer configuration
            $mailConfig = [
                'transport' => 'smtp',
                'host' => $this->config['host'],
                'port' => $this->config['port'],
                'encryption' => $this->config['encryption'],
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'timeout' => 30,
                'local_domain' => null,
            ];

            // Configure the mailer
            Config::set('mail.mailers.custom_smtp', $mailConfig);
            Config::set('mail.default', 'custom_smtp');

            Log::info('Custom mailer configured', [
                'host' => $this->config['host'],
                'port' => $this->config['port'],
                'encryption' => $this->config['encryption'],
                'username' => $this->config['username']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to configure custom mailer', [
                'error' => $e->getMessage(),
                'config' => $this->config
            ]);
        }
    }

    /**
     * Send an HTML email
     */
    public function sendHtmlEmail($to, $subject, $htmlContent, $from = null, $attachments = [])
    {
        try {
            $from = $from ?? $this->defaultFrom;

            // Configure mailer with custom SMTP settings if provided
            if ($this->config && isset($this->config['host'])) {
                $this->configureCustomMailer();
            }

            Mail::html($htmlContent, function (Message $message) use ($to, $subject, $from, $attachments) {
                $message->from($from['address'], $from['name'])
                        ->to($to)
                        ->subject($subject);

                // Add attachments if any
                foreach ($attachments as $attachment) {
                    if (isset($attachment['path']) && file_exists($attachment['path'])) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'] ?? basename($attachment['path']),
                            'mime' => $attachment['mime'] ?? null
                        ]);
                    }
                }
            });

            Log::info('HTML email sent successfully', [
                'to' => $to,
                'subject' => $subject,
                'from' => $from
            ]);

            return [
                'success' => true,
                'message' => 'HTML email sent successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send HTML email', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send email using a template
     */
    public function sendTemplateEmail($to, $subject, $template, $data = [], $from = null, $attachments = [])
    {
        try {
            $from = $from ?? $this->defaultFrom;

            // Configure mailer with custom SMTP settings if provided
            if ($this->config && isset($this->config['host'])) {
                $this->configureCustomMailer();
            }

            Mail::send($template, $data, function (Message $message) use ($to, $subject, $from, $attachments) {
                $message->from($from['address'], $from['name'])
                        ->to($to)
                        ->subject($subject);

                // Add attachments if any
                foreach ($attachments as $attachment) {
                    if (isset($attachment['path']) && file_exists($attachment['path'])) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'] ?? basename($attachment['path']),
                            'mime' => $attachment['mime'] ?? null
                        ]);
                    }
                }
            });

            Log::info('Template email sent successfully', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template,
                'from' => $from
            ]);

            return [
                'success' => true,
                'message' => 'Template email sent successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send template email', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send bulk emails
     */
    public function sendBulkEmails($recipients, $subject, $template, $data = [], $from = null, $attachments = [])
    {
        try {
            $from = $from ?? $this->defaultFrom;
            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            // Configure mailer with custom SMTP settings if provided
            if ($this->config && isset($this->config['host'])) {
                $this->configureCustomMailer();
            }

            foreach ($recipients as $recipient) {
                try {
                    $result = $this->sendTemplateEmail(
                        $recipient['email'],
                        $subject,
                        $template,
                        array_merge($data, ['recipient' => $recipient]),
                        $from,
                        $attachments
                    );

                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failedCount++;
                        $errors[] = [
                            'email' => $recipient['email'],
                            'error' => $result['error']
                        ];
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'email' => $recipient['email'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            Log::info('Bulk email completed', [
                'total' => count($recipients),
                'success' => $successCount,
                'failed' => $failedCount
            ]);

            return [
                'success' => true,
                'total' => count($recipients),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send bulk email', [
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
     * Send WhatsApp ticket reply email
     */
    public function sendWhatsAppTicketReply($ticketData, $attachments = [])
    {
        try {
            $template = 'emails.whatsapp.ticket-reply';
            $subject = "Re: {$ticketData['subject']}";
            
            $data = [
                'ticket_id' => $ticketData['ticket_id'],
                'message' => $ticketData['message'],
                'contact_name' => $ticketData['contact_name'],
                'agent_name' => $ticketData['agent_name'],
                'ticket_url' => $ticketData['ticket_url'] ?? null,
                'ticket_subject' => $ticketData['subject'],
                'sent_at' => now()->format('Y-m-d H:i:s')
            ];

            return $this->sendTemplateEmail(
                $ticketData['contact_email'],
                $subject,
                $template,
                $data,
                null,
                $attachments
            );

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp ticket reply email', [
                'ticket_data' => $ticketData,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test SMTP connection with provided credentials
     */
    public function testSmtpConnection()
    {
        try {
            if (!$this->config) {
                return [
                    'success' => false,
                    'message' => 'SMTP configuration not provided',
                    'error' => 'SMTP configuration not provided'
                ];
            }

            // Test basic SMTP configuration
            $config = [
                'host' => $this->config['host'] ?? null,
                'port' => $this->config['port'] ?? null,
                'encryption' => $this->config['encryption'] ?? null,
                'username' => $this->config['username'] ?? null,
                'password' => !empty($this->config['password']) ? '***configured***' : 'not configured'
            ];

            // Check if all required fields are present
            $missing = array_filter($config, function($value) {
                return $value === null;
            });

            if (!empty($missing)) {
                return [
                    'success' => false,
                    'message' => 'SMTP configuration incomplete',
                    'error' => 'Missing required SMTP configuration fields',
                    'missing_fields' => array_keys($missing),
                    'config' => $config
                ];
            }

            // Configure custom mailer for testing
            if (isset($this->config['host'])) {
                $this->configureCustomMailer();
            }

            // Try to send a test email to verify SMTP settings
            $testResult = $this->sendTextEmail(
                $this->defaultFrom['address'],
                'SMTP Test Email',
                'This is a test email to verify SMTP configuration.',
                $this->defaultFrom
            );

            if ($testResult['success']) {
                Log::info('SMTP connection test successful', $config);
                return [
                    'success' => true,
                    'message' => 'SMTP connection successful',
                    'config' => $config
                ];
            } else {
                Log::error('SMTP connection test failed', [
                    'config' => $config,
                    'error' => $testResult['error']
                ]);
                return [
                    'success' => false,
                    'message' => 'SMTP connection failed',
                    'error' => $testResult['error'],
                    'config' => $config
                ];
            }

        } catch (\Exception $e) {
            Log::error('SMTP connection test exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'SMTP connection test exception',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get SMTP configuration status
     */
    public function getSmtpConfigStatus()
    {
        if (!$this->config) {
            return [
                'configured' => false,
                'config' => null,
                'missing_fields' => ['all'],
                'message' => 'No SMTP configuration provided'
            ];
        }

        $config = [
            'host' => $this->config['host'] ?? null,
            'port' => $this->config['port'] ?? null,
            'encryption' => $this->config['encryption'] ?? null,
            'username' => $this->config['username'] ?? null,
            'password' => !empty($this->config['password']) ? '***configured***' : 'not configured',
            'from_address' => $this->defaultFrom['address'] ?? null,
            'from_name' => $this->defaultFrom['name'] ?? null
        ];

        $missing = array_filter($config, function($value) {
            return $value === null;
        });

        return [
            'configured' => empty($missing),
            'config' => $config,
            'missing_fields' => array_keys($missing)
        ];
    }
}
