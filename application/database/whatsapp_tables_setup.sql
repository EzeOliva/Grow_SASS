-- WhatsApp Tables Setup for Tenant Database
-- Run this SQL in your tenant database to enable WhatsApp functionality

-- Table structure for whatsapp_connections
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

-- Table structure for whatsapp_tickets
DROP TABLE IF EXISTS `whatsapp_tickets`;
CREATE TABLE `whatsapp_tickets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Multi-tenant support - references tenants table',
  `connection_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Reference to whatsapp_connections table',
  `contact_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Reference to whatsapp_contacts table',
  `contact_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contact name for this ticket',
  `contact_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Contact email',
  `contact_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Contact phone number',
  `assigned_to` int UNSIGNED NULL DEFAULT NULL COMMENT 'Assigned agent from users table',
  `status` enum('open','on_hold','in_progress','resolved','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `channel` enum('whatsapp','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'whatsapp',
  `subject` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Ticket description',
  `ticket_type_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Reference to whatsapp_ticket_types table',
  `tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'JSON array of tags',
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP COMMENT 'First message timestamp',
  `first_response_at` timestamp NULL DEFAULT NULL COMMENT 'Agent first response',
  `last_agent_message_at` timestamp NULL DEFAULT NULL COMMENT 'Last agent message',
  `last_client_message_at` timestamp NULL DEFAULT NULL COMMENT 'Last client message',
  `resolved_at` timestamp NULL DEFAULT NULL COMMENT 'Ticket resolution timestamp',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT 'Ticket closure timestamp',
  `resolution` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Resolution notes',
  `hold_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Reason for putting ticket on hold',
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Agent notes',
  `whatsapp_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'WhatsApp number for this ticket',
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Ticket category',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_tenant_status`(`tenant_id` ASC, `status` ASC) USING BTREE,
  INDEX `idx_tenant_connection`(`tenant_id` ASC, `connection_id` ASC) USING BTREE,
  INDEX `idx_tenant_contact`(`tenant_id` ASC, `contact_id` ASC) USING BTREE,
  INDEX `idx_tenant_assigned`(`tenant_id` ASC, `assigned_to` ASC) USING BTREE,
  INDEX `idx_tenant_channel`(`tenant_id` ASC, `channel` ASC) USING BTREE,
  INDEX `idx_opened_at`(`opened_at` ASC) USING BTREE,
  INDEX `idx_first_response_at`(`first_response_at` ASC) USING BTREE,
  INDEX `idx_resolved_at`(`resolved_at` ASC) USING BTREE,
  INDEX `idx_closed_at`(`closed_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Table structure for whatsapp_messages
DROP TABLE IF EXISTS `whatsapp_messages`;
CREATE TABLE `whatsapp_messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Multi-tenant support - references tenants table',
  `ticket_id` bigint UNSIGNED NOT NULL COMMENT 'Reference to whatsapp_tickets table',
  `sender_type` enum('client','agent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Who sent the message',
  `sender_id` int NULL DEFAULT NULL COMMENT 'User ID if agent, null if client',
  `sender_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the sender',
  `channel` enum('whatsapp','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'whatsapp',
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Message content',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT 'JSON array of attachment data',
  `message_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'External message ID (WhatsApp/Email)',
  `status` enum('sending','sent','delivered','read','failed','received') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'When message was read',
  `reply_to_message_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Reference to replied message',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT 'Additional message metadata',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_tenant_ticket`(`tenant_id` ASC, `ticket_id` ASC) USING BTREE,
  INDEX `idx_ticket_created`(`ticket_id` ASC, `created_at` ASC) USING BTREE,
  INDEX `idx_channel_status`(`channel` ASC, `status` ASC) USING BTREE,
  INDEX `idx_message_id`(`message_id` ASC) USING BTREE,
  INDEX `idx_sender_type`(`sender_type` ASC) USING BTREE,
  INDEX `idx_reply_to`(`reply_to_message_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Table structure for whatsapp_contacts
DROP TABLE IF EXISTS `whatsapp_contacts`;
CREATE TABLE `whatsapp_contacts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Multi-tenant support - references tenants table',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contact name',
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'WhatsApp phone number',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Contact email',
  `avatar` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Avatar image URL',
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Company name',
  `tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'JSON array of tags',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Additional notes',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this contact is active',
  `last_contact_at` timestamp NULL DEFAULT NULL COMMENT 'Last contact timestamp',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_tenant_phone`(`tenant_id` ASC, `phone_number` ASC) USING BTREE,
  INDEX `idx_tenant_email`(`tenant_id` ASC, `email` ASC) USING BTREE,
  INDEX `idx_tenant_active`(`tenant_id` ASC, `is_active` ASC) USING BTREE,
  INDEX `idx_last_contact`(`last_contact_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Table structure for whatsapp_quick_templates
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

-- Table structure for whatsapp_ticket_types
DROP TABLE IF EXISTS `whatsapp_ticket_types`;
CREATE TABLE `whatsapp_ticket_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Multi-tenant support - references tenants table',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type name',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Type description',
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '#007bff' COMMENT 'Hex color for display',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this type is active',
  `sort_order` int NOT NULL DEFAULT 0 COMMENT 'Sort order for display',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_tenant_active`(`tenant_id` ASC, `is_active` ASC) USING BTREE,
  INDEX `idx_sort_order`(`sort_order` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Table structure for whatsapp_tags
DROP TABLE IF EXISTS `whatsapp_tags`;
CREATE TABLE `whatsapp_tags` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int UNSIGNED NOT NULL COMMENT 'Multi-tenant support - references tenants table',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tag name',
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '#6c757d' COMMENT 'Hex color for display',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this tag is active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_tenant_active`(`tenant_id` ASC, `is_active` ASC) USING BTREE,
  INDEX `idx_name`(`name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Pivot table for ticket tags
DROP TABLE IF EXISTS `whatsapp_ticket_tag`;
CREATE TABLE `whatsapp_ticket_tag` (
  `whatsapp_ticket_id` bigint UNSIGNED NOT NULL,
  `whatsapp_tag_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`whatsapp_ticket_id`, `whatsapp_tag_id`),
  INDEX `idx_ticket_id`(`whatsapp_ticket_id` ASC) USING BTREE,
  INDEX `idx_tag_id`(`whatsapp_tag_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- Insert some default ticket types
INSERT INTO `whatsapp_ticket_types` (`tenant_id`, `name`, `description`, `color`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Support', 'Technical support and assistance', '#007bff', 1, 1, NOW(), NOW()),
(1, 'Sales', 'Sales inquiries and questions', '#28a745', 1, 2, NOW(), NOW()),
(1, 'Billing', 'Billing and payment issues', '#ffc107', 1, 3, NOW(), NOW()),
(1, 'General', 'General inquiries and information', '#6c757d', 1, 4, NOW(), NOW());

-- Insert some default quick templates
INSERT INTO `whatsapp_quick_templates` (`tenant_id`, `name`, `content`, `category`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Greeting', 'Hello! Thank you for contacting us. How can I help you today?', 'greeting', 1, 1, NOW(), NOW()),
(1, 'Thank You', 'Thank you for your message. We appreciate your business!', 'closing', 1, 2, NOW(), NOW()),
(1, 'Follow Up', 'I will follow up on this and get back to you shortly.', 'follow-up', 1, 3, NOW(), NOW()),
(1, 'Support Request', 'I understand you need technical support. Let me assist you with that.', 'support', 1, 4, NOW(), NOW());

-- Insert some default tags
INSERT INTO `whatsapp_tags` (`tenant_id`, `name`, `color`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'VIP', '#dc3545', 1, NOW(), NOW()),
(1, 'New Customer', '#28a745', 1, NOW(), NOW()),
(1, 'Urgent', '#fd7e14', 1, NOW(), NOW()),
(1, 'Follow Up Required', '#6f42c1', 1, NOW(), NOW());

-- Add foreign key constraints (optional - uncomment if you want referential integrity)
-- ALTER TABLE `whatsapp_tickets` ADD CONSTRAINT `fk_tickets_connection` FOREIGN KEY (`connection_id`) REFERENCES `whatsapp_connections` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
-- ALTER TABLE `whatsapp_tickets` ADD CONSTRAINT `fk_tickets_contact` FOREIGN KEY (`contact_id`) REFERENCES `whatsapp_contacts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
-- ALTER TABLE `whatsapp_tickets` ADD CONSTRAINT `fk_tickets_type` FOREIGN KEY (`ticket_type_id`) REFERENCES `whatsapp_ticket_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
-- ALTER TABLE `whatsapp_messages` ADD CONSTRAINT `fk_messages_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `whatsapp_tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- ALTER TABLE `whatsapp_ticket_tag` ADD CONSTRAINT `fk_ticket_tag_ticket` FOREIGN KEY (`whatsapp_ticket_id`) REFERENCES `whatsapp_tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- ALTER TABLE `whatsapp_ticket_tag` ADD CONSTRAINT `fk_ticket_tag_tag` FOREIGN KEY (`whatsapp_tag_id`) REFERENCES `whatsapp_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
