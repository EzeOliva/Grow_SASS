-- Support Agents module (Soporte > Agentes IA)
-- Fecha: 31-03-2026
-- Motor sugerido: InnoDB

CREATE TABLE IF NOT EXISTS `support_agents` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`tenant_id` INT UNSIGNED DEFAULT NULL,
	`agent_creatorid` INT UNSIGNED DEFAULT NULL,
	`agent_name` VARCHAR(255) NOT NULL,
	`agent_identity_prompt` TEXT NOT NULL,
	`agent_visibility` ENUM('team','client','everyone') NOT NULL DEFAULT 'team',
	`is_active` TINYINT(1) NOT NULL DEFAULT 1,
	`allow_client_chat` TINYINT(1) NOT NULL DEFAULT 0,
	`allow_ticket_suggestions` TINYINT(1) NOT NULL DEFAULT 0,
	`allow_document_sources` TINYINT(1) NOT NULL DEFAULT 1,
	`agent_settings` JSON DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `support_agents_tenant_id_index` (`tenant_id`),
	KEY `support_agents_agent_creatorid_index` (`agent_creatorid`),
	KEY `support_agents_agent_visibility_index` (`agent_visibility`),
	KEY `support_agents_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `support_agent_kb_categories` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`agent_id` BIGINT UNSIGNED NOT NULL,
	`kbcategory_id` INT UNSIGNED NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `support_agent_kb_unique` (`agent_id`, `kbcategory_id`),
	KEY `support_agent_kb_categories_kbcategory_id_index` (`kbcategory_id`),
	CONSTRAINT `support_agent_kb_categories_agent_id_fk`
		FOREIGN KEY (`agent_id`) REFERENCES `support_agents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `support_agent_documents` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`agent_id` BIGINT UNSIGNED NOT NULL,
	`agent_document_name` VARCHAR(255) NOT NULL,
	`agent_document_original_name` VARCHAR(255) DEFAULT NULL,
	`agent_document_mime` VARCHAR(120) DEFAULT NULL,
	`agent_document_size` BIGINT UNSIGNED DEFAULT NULL,
	`agent_document_disk` VARCHAR(60) DEFAULT NULL,
	`agent_document_path` VARCHAR(255) DEFAULT NULL,
	`agent_document_visibility` ENUM('team','client','everyone') NOT NULL DEFAULT 'team',
	`agent_document_status` ENUM('pending','processing','ready','failed') NOT NULL DEFAULT 'pending',
	`agent_document_extracted_text` LONGTEXT DEFAULT NULL,
	`agent_document_chunks` INT UNSIGNED NOT NULL DEFAULT 0,
	`agent_document_last_indexed_at` TIMESTAMP NULL DEFAULT NULL,
	`agent_document_error` TEXT DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `support_agent_documents_agent_document_visibility_index` (`agent_document_visibility`),
	KEY `support_agent_documents_agent_document_status_index` (`agent_document_status`),
	CONSTRAINT `support_agent_documents_agent_id_fk`
		FOREIGN KEY (`agent_id`) REFERENCES `support_agents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `support_agent_ticket_suggestions` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`agent_id` BIGINT UNSIGNED DEFAULT NULL,
	`ticket_id` INT UNSIGNED DEFAULT NULL,
	`suggestion_creatorid` INT UNSIGNED DEFAULT NULL,
	`suggestion_status` VARCHAR(40) NOT NULL DEFAULT 'proposed',
	`model_name` VARCHAR(120) DEFAULT NULL,
	`prompt_version` VARCHAR(60) DEFAULT NULL,
	`model_tokens_prompt` INT DEFAULT NULL,
	`model_tokens_completion` INT DEFAULT NULL,
	`suggestion_text` LONGTEXT NOT NULL,
	`suggestion_sources` LONGTEXT DEFAULT NULL,
	`suggestion_used_at` TIMESTAMP NULL DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `support_agent_ticket_suggestions_agent_id_index` (`agent_id`),
	KEY `support_agent_ticket_suggestions_ticket_id_index` (`ticket_id`),
	KEY `support_agent_ticket_suggestions_suggestion_creatorid_index` (`suggestion_creatorid`),
	KEY `support_agent_ticket_suggestions_suggestion_status_index` (`suggestion_status`),
	CONSTRAINT `support_agent_ticket_suggestions_agent_id_fk`
		FOREIGN KEY (`agent_id`) REFERENCES `support_agents`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `support_agent_test_runs` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`agent_id` BIGINT UNSIGNED DEFAULT NULL,
	`test_creatorid` INT UNSIGNED DEFAULT NULL,
	`test_audience` ENUM('team','client') NOT NULL DEFAULT 'team',
	`test_question` LONGTEXT NOT NULL,
	`test_answer` LONGTEXT DEFAULT NULL,
	`test_sources` LONGTEXT DEFAULT NULL,
	`response_status` VARCHAR(40) NOT NULL DEFAULT 'answered',
	`unanswered_reasons` TEXT DEFAULT NULL,
	`model_name` VARCHAR(120) DEFAULT NULL,
	`model_tokens_prompt` INT DEFAULT NULL,
	`model_tokens_completion` INT DEFAULT NULL,
	`model_tokens_total` INT DEFAULT NULL,
	`error_message` TEXT DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `support_agent_test_runs_agent_id_index` (`agent_id`),
	KEY `support_agent_test_runs_test_creatorid_index` (`test_creatorid`),
	KEY `support_agent_test_runs_test_audience_index` (`test_audience`),
	KEY `support_agent_test_runs_response_status_index` (`response_status`),
	CONSTRAINT `support_agent_test_runs_agent_id_fk`
		FOREIGN KEY (`agent_id`) REFERENCES `support_agents`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `support_agent_unanswered_queries` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`agent_id` BIGINT UNSIGNED DEFAULT NULL,
	`test_run_id` BIGINT UNSIGNED DEFAULT NULL,
	`unanswered_creatorid` INT UNSIGNED DEFAULT NULL,
	`unanswered_audience` ENUM('team','client') NOT NULL DEFAULT 'team',
	`unanswered_question` LONGTEXT NOT NULL,
	`unanswered_reason` VARCHAR(120) DEFAULT NULL,
	`unanswered_reason_details` TEXT DEFAULT NULL,
	`unanswered_status` VARCHAR(40) NOT NULL DEFAULT 'open',
	`resolved_by` INT UNSIGNED DEFAULT NULL,
	`resolved_at` TIMESTAMP NULL DEFAULT NULL,
	`resolution_notes` TEXT DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `support_agent_unanswered_queries_agent_id_index` (`agent_id`),
	KEY `support_agent_unanswered_queries_test_run_id_index` (`test_run_id`),
	KEY `support_agent_unanswered_queries_unanswered_creatorid_index` (`unanswered_creatorid`),
	KEY `support_agent_unanswered_queries_unanswered_audience_index` (`unanswered_audience`),
	KEY `support_agent_unanswered_queries_unanswered_reason_index` (`unanswered_reason`),
	KEY `support_agent_unanswered_queries_unanswered_status_index` (`unanswered_status`),
	KEY `support_agent_unanswered_queries_resolved_by_index` (`resolved_by`),
	CONSTRAINT `support_agent_unanswered_queries_agent_id_fk`
		FOREIGN KEY (`agent_id`) REFERENCES `support_agents`(`id`) ON DELETE SET NULL,
	CONSTRAINT `support_agent_unanswered_queries_test_run_id_fk`
		FOREIGN KEY (`test_run_id`) REFERENCES `support_agent_test_runs`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

