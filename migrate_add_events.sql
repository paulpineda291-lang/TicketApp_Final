-- ============================================================
--  HAU DB — Migration: Add Events Support
--  Safe to run on existing hau_db — does NOT drop any tables
--  or delete any existing rows.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- --------------------------------------------------------
-- 1. Create the events table (only if it doesn't exist yet)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `event_date` date NOT NULL,
  `venue` varchar(200) NOT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Insert sample events (only if the table is empty)
-- --------------------------------------------------------
INSERT INTO `events` (`id`, `name`, `event_date`, `venue`, `banner_image`, `description`, `is_active`)
SELECT 1, 'HAU University Days 2026', '2026-03-25', 'HAU Main Campus',     'university_days.jpg', 'The biggest celebration of the year at Holy Angel University!', 1
WHERE NOT EXISTS (SELECT 1 FROM `events` WHERE id = 1);

INSERT INTO `events` (`id`, `name`, `event_date`, `venue`, `banner_image`, `description`, `is_active`)
SELECT 2, 'HAU Foundation Day',       '2026-04-10', 'HAU Auditorium',      'foundation_day.jpg',  'Celebrating the founding of Holy Angel University.',            1
WHERE NOT EXISTS (SELECT 1 FROM `events` WHERE id = 2);

INSERT INTO `events` (`id`, `name`, `event_date`, `venue`, `banner_image`, `description`, `is_active`)
SELECT 3, 'HAU Sports Fest 2026',     '2026-05-05', 'HAU Covered Courts',  'sports_fest.jpg',     'Annual inter-department sports competition.',                   1
WHERE NOT EXISTS (SELECT 1 FROM `events` WHERE id = 3);

-- --------------------------------------------------------
-- 3. Add event_id column to ticket_types (if not present)
-- --------------------------------------------------------
ALTER TABLE `ticket_types`
  ADD COLUMN IF NOT EXISTS `event_id` int(10) UNSIGNED NULL
  AFTER `id`;

-- Your existing ticket types:
--   id=1  Guest Ticket   (guest)   → University Days (event 1)
--   id=2  Student Ticket (student) → University Days (event 1)
-- Link them to University Days so existing data stays consistent.
UPDATE `ticket_types` SET `event_id` = 1 WHERE `event_id` IS NULL;

-- Now make the column NOT NULL and add the foreign key
ALTER TABLE `ticket_types`
  MODIFY COLUMN `event_id` int(10) UNSIGNED NOT NULL;

-- Add FK only if it doesn't already exist
SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME        = 'ticket_types'
    AND CONSTRAINT_NAME   = 'fk_ticket_event'
    AND CONSTRAINT_TYPE   = 'FOREIGN KEY'
);

SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE `ticket_types` ADD CONSTRAINT `fk_ticket_event`
   FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)',
  'SELECT "FK fk_ticket_event already exists, skipping."'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --------------------------------------------------------
-- 4. Insert ticket types for the 2 new events
-- --------------------------------------------------------
INSERT IGNORE INTO `ticket_types` (`event_id`, `name`, `price`, `allowed_for`, `is_active`) VALUES
-- Foundation Day
(2, 'Guest Ticket',   150.00, 'guest',   1),
(2, 'Student Ticket', 100.00, 'student', 1),
-- Sports Fest
(3, 'Guest Ticket',   100.00, 'guest',   1),
(3, 'Student Ticket',  50.00, 'student', 1);

-- --------------------------------------------------------
-- 5. Add event_id column to orders (if not present)
-- --------------------------------------------------------
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `event_id` int(10) UNSIGNED NULL
  AFTER `user_id`;

-- Existing orders were all for University Days (event 1)
UPDATE `orders` SET `event_id` = 1 WHERE `event_id` IS NULL;

-- Make NOT NULL
ALTER TABLE `orders`
  MODIFY COLUMN `event_id` int(10) UNSIGNED NOT NULL;

-- Add FK only if missing
SET @fk_exists2 = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME        = 'orders'
    AND CONSTRAINT_NAME   = 'orders_ibfk_event'
    AND CONSTRAINT_TYPE   = 'FOREIGN KEY'
);

SET @sql2 = IF(@fk_exists2 = 0,
  'ALTER TABLE `orders` ADD CONSTRAINT `orders_ibfk_event`
   FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)',
  'SELECT "FK orders_ibfk_event already exists, skipping."'
);
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

-- --------------------------------------------------------
-- Done!
-- --------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- Quick sanity check — run this after import to verify:
-- SELECT * FROM events;
-- SELECT * FROM ticket_types;
-- SELECT id, user_id, event_id, status FROM orders LIMIT 5;
