-- WhatsApp Setup for Tenant Database (Based on growcrm_landlord.sql)
-- Run this SQL in your tenant database to enable WhatsApp functionality
-- IMPORTANT: Update tenant_id values to match your actual tenant ID

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Table structure for whatsapp_connections (from landlord)
DROP TABLE IF EXISTS `whatsapp_connections`;
CREATE TABLE `whatsapp_connections` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Multi-tenant support - references tenants table',
  `connection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Friendly name for this connection',
  `connection_type` enum('baileys','twilio','360dialog','gupshup') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'WhatsApp connection method',
  `status` enum('disconnected','connecting','connected','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disconnected',
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'WhatsApp phone number',
  `connection_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'JSON data for connection (API keys, tokens, etc.)',
  `qr_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'QR code data for Baileys',
  `last_connected_at` timestamp NULL DEFAULT NULL COMMENT 'Last successful connection',
  `last_error_at` timestamp NULL DEFAULT NULL COMMENT 'Last connection error',
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Last error message',
  `webhook_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT 'Webhook configuration',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this connection is active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_tenant_status`(`tenant_id` ASC, `status` ASC) USING BTREE,
  INDEX `idx_tenant_type`(`tenant_id` ASC, `connection_type` ASC) USING BTREE,
  INDEX `idx_phone_number`(`phone_number` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Table structure for whatsapp_tickets (from landlord)
DROP TABLE IF EXISTS `whatsapp_tickets`;
CREATE TABLE `whatsapp_tickets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Multi-tenant support - references tenants table',
  `contact_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contact name for this ticket',
  `contact_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Contact email',
  `contact_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Contact phone number',
  `agent_id` int UNSIGNED NULL DEFAULT NULL COMMENT 'Assigned agent from users table',
  `status` enum('open','in_progress','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `channel` enum('whatsapp','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'whatsapp',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'JSON array of tags',
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP COMMENT 'First message timestamp',
  `first_response_at` timestamp NULL DEFAULT NULL COMMENT 'Agent first response',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT 'Ticket closure timestamp',
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Agent notes',
  `whatsapp_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'WhatsApp number for this ticket',
  `priority` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium' COMMENT 'low, medium, high, urgent',
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Ticket category',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `whatsapp_tickets_tenant_id_status_index`(`tenant_id` ASC, `status` ASC) USING BTREE,
  INDEX `whatsapp_tickets_tenant_id_agent_id_index`(`tenant_id` ASC, `agent_id` ASC) USING BTREE,
  INDEX `whatsapp_tickets_tenant_id_contact_email_index`(`tenant_id` ASC, `contact_email` ASC) USING BTREE,
  INDEX `whatsapp_tickets_tenant_id_channel_index`(`tenant_id` ASC, `channel` ASC) USING BTREE,
  INDEX `whatsapp_tickets_opened_at_index`(`opened_at` ASC) USING BTREE,
  INDEX `whatsapp_tickets_first_response_at_index`(`first_response_at` ASC) USING BTREE,
  INDEX `whatsapp_tickets_closed_at_index`(`closed_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Table structure for whatsapp_messages (from landlord)
DROP TABLE IF EXISTS `whatsapp_messages`;
CREATE TABLE `whatsapp_messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint UNSIGNED NOT NULL COMMENT 'Reference to whatsapp_tickets table',
  `sender_type` enum('client','agent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Who sent the message',
  `sender_id` int NULL DEFAULT NULL COMMENT 'User ID if agent, null if client',
  `sender_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the sender',
  `channel` enum('whatsapp','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'whatsapp',
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Message content',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT 'JSON array of attachment data',
  `message_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'External message ID (WhatsApp/Email)',
  `status` enum('sent','delivered','read','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'When message was read',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT 'Additional message metadata',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_ticket_id`(`ticket_id` ASC) USING BTREE,
  INDEX `idx_ticket_created`(`ticket_id` ASC, `created_at` ASC) USING BTREE,
  INDEX `idx_channel_status`(`channel` ASC, `status` ASC) USING BTREE,
  INDEX `idx_message_id`(`message_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Additional table needed for our integration: whatsapp_quick_templates
DROP TABLE IF EXISTS `whatsapp_quick_templates`;
CREATE TABLE `whatsapp_quick_templates` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Multi-tenant support - references tenants table',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Template name',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Template content',
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Template category',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this template is active',
  `sort_order` int NOT NULL DEFAULT 0 COMMENT 'Sort order for display',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_tenant_active`(`tenant_id` ASC, `is_active` ASC) USING BTREE,
  INDEX `idx_tenant_category`(`tenant_id` ASC, `category` ASC) USING BTREE,
  INDEX `idx_sort_order`(`sort_order` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Insert sample data (UPDATED for tenant_id = 3)
INSERT INTO `whatsapp_quick_templates` (`tenant_id`, `name`, `content`, `category`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(3, 'Greeting', 'Hello! Thank you for contacting us. How can I help you today?', 'greeting', 1, 1, NOW(), NOW()),
(3, 'Thank You', 'Thank you for your message. We appreciate your business!', 'closing', 1, 2, NOW(), NOW()),
(3, 'Follow Up', 'I will follow up on this and get back to you shortly.', 'follow-up', 1, 3, NOW(), NOW());

-- Insert sample WhatsApp connection (UPDATED for tenant_id = 3)
INSERT INTO `whatsapp_connections` (`tenant_id`, `connection_name`, `connection_type`, `status`, `phone_number`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'Main WhatsApp Business', 'baileys', 'disconnected', '+1234567890', 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;

-- ✅ UPDATED: tenant_id values set to 3
-- Your tenant ID is: 3
