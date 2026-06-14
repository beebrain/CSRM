-- Update database schema for new assessment workflow

-- 1. Add plagiarism columns and presentation score to papers table
ALTER TABLE `papers` 
ADD COLUMN `plagiarism_status` ENUM('pending', 'checked', 'failed') NOT NULL DEFAULT 'pending',
ADD COLUMN `similarity_percent` INT DEFAULT NULL,
ADD COLUMN `plagiarism_report_url` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `presentation_score` DECIMAL(5,2) DEFAULT NULL;

-- 2. Add default revision days to conferences table
ALTER TABLE `conferences`
ADD COLUMN `default_revision_days` INT NOT NULL DEFAULT 30;

-- 3. Add revision deadline to papers table
ALTER TABLE `papers`
ADD COLUMN `revision_deadline` DATETIME DEFAULT NULL;
