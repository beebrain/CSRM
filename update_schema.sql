-- Modify papers status ENUM
ALTER TABLE `papers` MODIFY COLUMN `status` ENUM('submitted', 'under_review', 'revision_required', 'revised_submitted', 'passed_round1', 'failed_round1', 'passed_round2', 'failed_round2', 'withdrawn') NOT NULL DEFAULT 'submitted';

-- Modify paper_reviews decision ENUM
ALTER TABLE `paper_reviews` MODIFY COLUMN `decision` ENUM('pass', 'fail', 'revision') DEFAULT NULL;

-- Add keywords to papers table
ALTER TABLE `papers` ADD COLUMN `keywords` VARCHAR(255) DEFAULT NULL;

-- Create user_expertise table
CREATE TABLE IF NOT EXISTS `user_expertise` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `keyword` VARCHAR(100) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_keyword` (`user_id`, `keyword`),
  CONSTRAINT `fk_expertise_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create paper_revisions table
CREATE TABLE IF NOT EXISTS `paper_revisions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `paper_id` INT UNSIGNED NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `comments` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_revision_paper` FOREIGN KEY (`paper_id`) REFERENCES `papers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create evaluation_templates table
CREATE TABLE IF NOT EXISTS `evaluation_templates` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `round` TINYINT(1) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create evaluation_template_criteria table
CREATE TABLE IF NOT EXISTS `evaluation_template_criteria` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `template_id` INT UNSIGNED NOT NULL,
  `criteria_name` VARCHAR(255) NOT NULL,
  `max_score` INT NOT NULL DEFAULT 100,
  `description` TEXT DEFAULT NULL,
  CONSTRAINT `fk_template_criteria_tmpl` FOREIGN KEY (`template_id`) REFERENCES `evaluation_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add open/close phase flags to conferences table
ALTER TABLE `conferences` 
ADD COLUMN `accept_submissions` TINYINT(1) NOT NULL DEFAULT 1,
ADD COLUMN `accept_evaluations` TINYINT(1) NOT NULL DEFAULT 1,
ADD COLUMN `accept_grading` TINYINT(1) NOT NULL DEFAULT 1;

