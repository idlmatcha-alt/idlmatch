CREATE TABLE contacts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(320) NOT NULL UNIQUE,
  name VARCHAR(190) NULL,
  source_domain VARCHAR(190) NULL,
  source_url TEXT NULL,
  source_title TEXT NULL,
  consent_status ENUM('pending','approved','unsubscribed','blocked') NOT NULL DEFAULT 'pending',
  first_seen_at DATETIME NOT NULL,
  last_seen_at DATETIME NOT NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_consent (consent_status),
  INDEX idx_domain (source_domain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE crawl_sources (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  url TEXT NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  max_depth INT NOT NULL DEFAULT 2,
  last_run_at DATETIME NULL,
  last_found INT NOT NULL DEFAULT 0,
  last_error TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE campaigns (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  brand ENUM('idlfx.com','idlmatch.com') NOT NULL,
  subject VARCHAR(255) NOT NULL,
  body_html MEDIUMTEXT NOT NULL,
  body_text MEDIUMTEXT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE send_log (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  campaign_id INT UNSIGNED NULL,
  contact_id BIGINT UNSIGNED NOT NULL,
  to_email VARCHAR(320) NOT NULL,
  status ENUM('sent','failed','skipped') NOT NULL,
  response TEXT NULL,
  sent_at DATETIME NOT NULL,
  INDEX idx_campaign (campaign_id),
  INDEX idx_contact (contact_id),
  INDEX idx_sent (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE app_settings (
  setting_key VARCHAR(190) PRIMARY KEY,
  setting_value TEXT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
