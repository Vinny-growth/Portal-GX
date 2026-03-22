-- Wealth Manager Schema (idempotent)
-- Execute in DB `portal` (or your configured database)

CREATE TABLE IF NOT EXISTS `wm_user_profile` (
  `user_id` INT(11) UNSIGNED NOT NULL,
  `estado_civil` VARCHAR(50) DEFAULT NULL,
  `ano_nascimento` INT(4) DEFAULT NULL,
  `perfil_risco` VARCHAR(30) DEFAULT NULL,
  `horizonte` VARCHAR(50) DEFAULT NULL,
  `observacoes` TEXT DEFAULT NULL,
  `consent_accepted_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_income_expense` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `tipo` VARCHAR(10) NOT NULL,
  `categoria` VARCHAR(100) DEFAULT NULL,
  `valor_mensal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_wm_ie_user` (`user_id`),
  KEY `idx_wm_ie_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_assets_financial` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `classe` VARCHAR(50) NOT NULL,
  `subtipo` VARCHAR(100) DEFAULT NULL,
  `valor_atual` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_wm_af_user` (`user_id`),
  KEY `idx_wm_af_classe` (`classe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_assets_realestate` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `tipo` VARCHAR(100) DEFAULT NULL,
  `valor_estimado` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `renda_aluguel` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `saldo_divida` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_wm_ar_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_business_holdings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `nome` VARCHAR(255) DEFAULT NULL,
  `participacao_pct` DECIMAL(7,2) NOT NULL DEFAULT 0.00,
  `observacoes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wm_bh_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_liabilities` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `tipo` VARCHAR(100) DEFAULT NULL,
  `saldo_atual` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `taxa_aprox` DECIMAL(7,3) NOT NULL DEFAULT 0.000,
  `prazo_meses` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_wm_li_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_goals` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `nome_meta` VARCHAR(255) NOT NULL,
  `valor_objetivo` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `prazo_meses` INT(11) NOT NULL DEFAULT 0,
  `prioridade` VARCHAR(20) DEFAULT NULL,
  `observacoes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wm_go_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_sessions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `started_at` DATETIME DEFAULT NULL,
  `ended_at` DATETIME DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'ativa',
  `messages_count` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_wm_se_user` (`user_id`),
  KEY `idx_wm_se_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_messages` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` INT(11) UNSIGNED NOT NULL,
  `role` VARCHAR(10) NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wm_me_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_tokens` (
  `user_id` INT(11) UNSIGNED NOT NULL,
  `tokens_disponiveis` INT(11) NOT NULL DEFAULT 0,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_settings` (
  `chave` VARCHAR(191) NOT NULL,
  `valor` TEXT DEFAULT NULL,
  PRIMARY KEY (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_appointments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED DEFAULT NULL,
  `nome` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `telefone` VARCHAR(50) DEFAULT NULL,
  `preferencia_horario` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'novo',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wm_ap_user` (`user_id`),
  KEY `idx_wm_ap_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wm_audit_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT(11) UNSIGNED NOT NULL,
  `acao` VARCHAR(100) NOT NULL,
  `detalhes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wm_al_admin` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

