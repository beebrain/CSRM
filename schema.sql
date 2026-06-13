-- Enable strict mode and set collation
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `payment_items`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `presentation_review_scores`;
DROP TABLE IF EXISTS `presentation_reviews`;
DROP TABLE IF EXISTS `room_papers`;
DROP TABLE IF EXISTS `room_committees`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `paper_review_scores`;
DROP TABLE IF EXISTS `paper_reviews`;
DROP TABLE IF EXISTS `evaluation_criteria`;
DROP TABLE IF EXISTS `papers`;
DROP TABLE IF EXISTS `disciplines`;
DROP TABLE IF EXISTS `tracks`;
DROP TABLE IF EXISTS `conference_admins`;
DROP TABLE IF EXISTS `conferences`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table (Global access across years)
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `affiliation` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('superadmin', 'admin', 'reviewer', 'committee', 'author') NOT NULL DEFAULT 'author',
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `verification_token` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Conferences Table (Year-based settings)
CREATE TABLE `conferences` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `year` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `host_name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
  `accept_submissions` TINYINT(1) NOT NULL DEFAULT 1,
  `accept_evaluations` TINYINT(1) NOT NULL DEFAULT 1,
  `accept_grading` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Conference Admins (Map admins to specific conference years)
CREATE TABLE `conference_admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conference_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_conf_user` (`conference_id`, `user_id`),
  CONSTRAINT `fk_conf_admin_conf` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_conf_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tracks Table (Global paper submission categories)
CREATE TABLE `tracks` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL, -- สัมมนา, โปรเจค, โปสเตอร์
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Disciplines Table (Mathematical fields under tracks - CRUD by Admins)
CREATE TABLE `disciplines` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `track_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL, -- พีชคณิต, ทฤษฎีเซต, แคลคูลัส
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_discipline_track` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Papers Table (Submissions per conference)
CREATE TABLE `papers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conference_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `abstract` TEXT NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `author_id` INT UNSIGNED NOT NULL,
  `discipline_id` INT UNSIGNED NOT NULL,
  `status` ENUM('submitted', 'under_review', 'passed_round1', 'failed_round1', 'passed_round2', 'failed_round2', 'withdrawn') NOT NULL DEFAULT 'submitted',
  `payment_status` ENUM('unpaid', 'pending_verification', 'paid') NOT NULL DEFAULT 'unpaid',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  -- Optimized composite indexes for queries and dashboard stats
  INDEX `idx_paper_conf_status` (`conference_id`, `status`),
  INDEX `idx_paper_conf_payment` (`conference_id`, `payment_status`),
  CONSTRAINT `fk_paper_conf` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_paper_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_paper_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `disciplines` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Evaluation Criteria Table (Dynamic rubric settings per conference year)
CREATE TABLE `evaluation_criteria` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conference_id` INT UNSIGNED NOT NULL,
  `round` TINYINT(1) NOT NULL, -- 1 = Peer Review, 2 = Presentation
  `criteria_name` VARCHAR(255) NOT NULL,
  `max_score` INT NOT NULL DEFAULT 100,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_criteria_conf` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Paper Reviews Table (Round 1 Peer Reviews - >= 3 Reviewers)
CREATE TABLE `paper_reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `paper_id` INT UNSIGNED NOT NULL,
  `reviewer_id` INT UNSIGNED NOT NULL,
  `status` ENUM('pending', 'completed') NOT NULL DEFAULT 'pending',
  `decision` ENUM('pass', 'fail') DEFAULT NULL,
  `comments` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_paper_reviewer` (`paper_id`, `reviewer_id`),
  CONSTRAINT `fk_review_paper` FOREIGN KEY (`paper_id`) REFERENCES `papers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Paper Review Scores Table (Scores for dynamic criteria in Round 1)
CREATE TABLE `paper_review_scores` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `review_id` INT UNSIGNED NOT NULL,
  `criteria_id` INT UNSIGNED NOT NULL,
  `score` INT NOT NULL,
  CONSTRAINT `fk_score_review` FOREIGN KEY (`review_id`) REFERENCES `paper_reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_score_criteria` FOREIGN KEY (`criteria_id`) REFERENCES `evaluation_criteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Rooms Table (Presentation Rooms for Round 2)
CREATE TABLE `rooms` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conference_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `date_time` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_room_conf` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Room Committees (Assign committees to specific rooms)
CREATE TABLE `room_committees` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT UNSIGNED NOT NULL,
  `committee_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_room_comm` (`room_id`, `committee_id`),
  CONSTRAINT `fk_room_comm_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_room_comm_user` FOREIGN KEY (`committee_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Room Papers Table (Schedule papers in rooms)
CREATE TABLE `room_papers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT UNSIGNED NOT NULL,
  `paper_id` INT UNSIGNED NOT NULL,
  `presentation_time` TIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_room_paper` (`room_id`, `paper_id`),
  CONSTRAINT `fk_room_paper_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_room_paper_paper` FOREIGN KEY (`paper_id`) REFERENCES `papers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Presentation Reviews Table (Round 2 Presentation Reviews - 3 room committees)
CREATE TABLE `presentation_reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `paper_id` INT UNSIGNED NOT NULL,
  `committee_id` INT UNSIGNED NOT NULL,
  `status` ENUM('pending', 'completed') NOT NULL DEFAULT 'pending',
  `decision` ENUM('pass', 'fail') DEFAULT NULL,
  `comments` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_pres_comm` (`paper_id`, `committee_id`),
  CONSTRAINT `fk_pres_rev_paper` FOREIGN KEY (`paper_id`) REFERENCES `papers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pres_rev_comm` FOREIGN KEY (`committee_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Presentation Review Scores Table (Scores for dynamic criteria in Round 2)
CREATE TABLE `presentation_review_scores` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `presentation_review_id` INT UNSIGNED NOT NULL,
  `criteria_id` INT UNSIGNED NOT NULL,
  `score` INT NOT NULL,
  CONSTRAINT `fk_pres_score_rev` FOREIGN KEY (`presentation_review_id`) REFERENCES `presentation_reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pres_score_crit` FOREIGN KEY (`criteria_id`) REFERENCES `evaluation_criteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Payments Table (Payments covering individual or groups of papers)
CREATE TABLE `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conference_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `payment_method` ENUM('bank_transfer', 'online_gateway') NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `slip_path` VARCHAR(255) DEFAULT NULL,
  `transaction_reference` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_payment_status` (`status`),
  INDEX `idx_payment_conf` (`conference_id`),
  CONSTRAINT `fk_payment_conf` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Payment Items (Paper assignments to payments for group checkout support)
CREATE TABLE `payment_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payment_id` INT UNSIGNED NOT NULL,
  `paper_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_item_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_paper` FOREIGN KEY (`paper_id`) REFERENCES `papers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- SEED DATA (Default Initial Records)
-- ==========================================

-- Seed Tracks
INSERT INTO `tracks` (`id`, `name`, `description`) VALUES
(1, 'สัมมนา (Seminar)', 'การนำเสนอเชิงสัมมนาเชิงลึกด้านคณิตศาสตร์และดุษฎีนิพนธ์'),
(2, 'โปรเจค (Project)', 'โครงงานคณิตศาสตร์หรืองานวิจัยประยุกต์ของระดับบัณฑิตศึกษา'),
(3, 'โปสเตอร์ (Poster)', 'การนำเสนอผลงานวิจัยด้วยโปสเตอร์วิชาการ');

-- Seed Disciplines (Algebra, Set Theory, Calculus)
INSERT INTO `disciplines` (`track_id`, `name`) VALUES
(1, 'พีชคณิต (Algebra - Seminar)'),
(1, 'ทฤษฎีเซต (Set Theory - Seminar)'),
(1, 'แคลคูลัส (Calculus - Seminar)'),
(2, 'พีชคณิตประยุกต์ (Applied Algebra - Project)'),
(2, 'การสร้างแบบจำลองทางคณิตศาสตร์ (Mathematical Modeling - Project)'),
(3, 'คณิตศาสตร์ศึกษา (Mathematics Education - Poster)'),
(3, 'การวิเคราะห์เชิงตัวเลข (Numerical Analysis - Poster)');

-- Seed Conferences (Year 2569)
INSERT INTO `conferences` (`id`, `year`, `title`, `host_name`, `description`, `is_active`) VALUES
(1, 2569, 'การประชุมวิชาการคณิตศาสตร์และคณิตศาสตร์ประยุกต์ระดับชาติ ครั้งที่ 14 (CSRM 2026)', 'มหาวิทยาลัยราชภัฏอุตรดิตถ์ (URU)', 'งานประชุมวิชาการคณิตศาสตร์ครั้งยิ่งใหญ่ เพื่อรวบรวมงานวิจัยทางด้านคณิตศาสตร์บริสุทธิ์และคณิตศาสตร์ประยุกต์', 1);

-- Seed Default Users
-- Default passwords: 'password123' (bcrypt hash: $2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.)
INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `role`, `is_verified`) VALUES
-- 1. Super Admin
(1, 'superadmin@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'สมชาย', 'แอดมินใหญ่', 'superadmin', 1),
-- 2. Conference Admin for Year 2569
(2, 'admin2569@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'วิชัย', 'แอดมินประจำปี', 'admin', 1),
-- 3. Reviewers (for paper evaluation, need at least 3)
(3, 'reviewer1@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'ดร.สมศักดิ์', 'พีชคณิตการประเมิน', 'reviewer', 1),
(4, 'reviewer2@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'ผศ.ดร.ดวงใจ', 'วิเคราะห์คณิตศาสตร์', 'reviewer', 1),
(5, 'reviewer3@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'รศ.นภา', 'สถิติวิจัย', 'reviewer', 1),
-- 4. Committees (for room presentation, need 3)
(6, 'committee1@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'ดร.อัมพร', 'สัจจะวาที', 'committee', 1),
(7, 'committee2@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'ผศ.ธีรพล', 'สมการทอง', 'committee', 1),
(8, 'committee3@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'ดร.ศิริลักษณ์', 'เมทริกซ์เจริญ', 'committee', 1),
-- 5. Submitter / Author
(9, 'author@csrm.com', '$2y$10$7wQJq9J.0K.1fS8L9G9Q.O5o5.S/6h.N6.P/7m1K9L/8t2u.o.2b.', 'ปรีชา', 'ส่งผลงาน', 'author', 1);

-- Map Admin to Year 2569 (Conference ID 1)
INSERT INTO `conference_admins` (`conference_id`, `user_id`) VALUES (1, 2);

-- Seed Evaluation Criteria for Conference 1 (Year 2569)
-- Round 1: Peer Review Criteria
INSERT INTO `evaluation_criteria` (`conference_id`, `round`, `criteria_name`, `max_score`, `description`) VALUES
(1, 1, 'ความสอดคล้องทางวิชาการ (Academic Relevance)', 25, 'บทความมีความสอดคล้องกับหัวข้อและมีประโยชน์ต่อศาสตร์คณิตศาสตร์'),
(1, 1, 'ระเบียบวิธีวิจัยและความถูกต้อง (Methodology & Correctness)', 35, 'การพิสูจน์ ทฤษฎีบท หรือการแก้ปัญหาทางคณิตศาสตร์มีความถูกต้องตามหลักเกณฑ์'),
(1, 1, 'ความใหม่และความสร้างสรรค์ (Originality & Novelty)', 20, 'บทความแสดงถึงผลลัพธ์หรือวิธีการวิจัยใหม่ที่ยังไม่เคยเผยแพร่มาก่อน'),
(1, 1, 'ความสมบูรณ์ของรูปเล่มบทคัดย่อ (Formatting & Writing)', 20, 'โครงสร้างภาษา รูปแบบเล่ม และการอ้างอิงเป็นไปตามมาตรฐานที่กำหนด');

-- Round 2: Presentation Review Criteria
INSERT INTO `evaluation_criteria` (`conference_id`, `round`, `criteria_name`, `max_score`, `description`) VALUES
(1, 2, 'เทคนิคการนำเสนอและสื่ออธิบาย (Presentation Technique)', 30, 'การออกแบบสไลด์ โปสเตอร์ และทักษะการพูดนำเสนอ'),
(1, 2, 'การจัดสรรเวลา (Time Management)', 20, 'นำเสนอและตอบคำถามภายในเวลาที่กำหนดอย่างกระชับ'),
(1, 2, 'การตอบคำถามและคำอธิบายเชิงวิชาการ (Q&A & Discussion)', 50, 'ความชัดเจนและความถูกต้องในการตอบข้อคำถามจากคณะกรรมการ');
