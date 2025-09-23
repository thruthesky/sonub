<?php
/**
 * Database Initialization Script
 * Creates database, tables, and initial data
 *
 * Uses Firebase Phone Authentication - no email/password fields
 */

// Load bootstrap functions first
require_once __DIR__ . '/../etc/boot/boot.functions.php';

// Load all necessary includes
require_once etc_folder('includes');

/**
 * Initialize database
 */
function init_database() {
    try {
        echo "Starting database initialization...\n";

        // Get database connection
        $db = db_connection();

        // Create database if not exists
        $dbName = DB_NAME;
        $createDbSql = "CREATE DATABASE IF NOT EXISTS `$dbName`
                        CHARACTER SET utf8mb4
                        COLLATE utf8mb4_unicode_ci";

        try {
            // Connect without specifying database first
            $hostConnection = new PDO(
                "mysql:host=" . DB_HOST . ";charset=utf8mb4",
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            $hostConnection->exec($createDbSql);
            echo "Database '$dbName' created or already exists.\n";
        } catch (PDOException $e) {
            echo "Note: Could not create database (may already exist): " . $e->getMessage() . "\n";
        }

        // Use the database
        $db->exec("USE `$dbName`");

        // Create users table with Firebase UID
        echo "Creating users table...\n";
        $createUsersTable = "
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `firebase_uid` VARCHAR(128) NOT NULL,
                `phone_number` VARCHAR(20) NOT NULL,
                `username` VARCHAR(100) NULL DEFAULT NULL,
                `display_name` VARCHAR(255) NULL DEFAULT NULL,
                `first_name` VARCHAR(100) NULL DEFAULT NULL,
                `last_name` VARCHAR(100) NULL DEFAULT NULL,
                `avatar_url` VARCHAR(500) NULL DEFAULT NULL,
                `status` ENUM('active', 'inactive', 'suspended', 'pending') DEFAULT 'active',
                `last_login_at` INT(11) UNSIGNED NULL DEFAULT NULL,
                `created_at` INT(11) UNSIGNED NOT NULL,
                `updated_at` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_firebase_uid` (`firebase_uid`),
                UNIQUE KEY `unique_phone_number` (`phone_number`),
                UNIQUE KEY `unique_username` (`username`),
                KEY `idx_status` (`status`),
                KEY `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->exec($createUsersTable);
        echo "Users table created successfully.\n";

        // Create sessions table
        echo "Creating sessions table...\n";
        $createSessionsTable = "
            CREATE TABLE IF NOT EXISTS `sessions` (
                `id` VARCHAR(128) NOT NULL,
                `user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
                `firebase_uid` VARCHAR(128) NULL DEFAULT NULL,
                `ip_address` VARCHAR(45) NULL DEFAULT NULL,
                `user_agent` TEXT NULL DEFAULT NULL,
                `payload` TEXT NOT NULL,
                `last_activity` INT(11) UNSIGNED NOT NULL,
                `expires_at` INT(11) UNSIGNED NULL DEFAULT NULL,
                `created_at` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_user_id` (`user_id`),
                KEY `idx_firebase_uid` (`firebase_uid`),
                KEY `idx_last_activity` (`last_activity`),
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->exec($createSessionsTable);
        echo "Sessions table created successfully.\n";

        // Create posts table
        echo "Creating posts table...\n";
        $createPostsTable = "
            CREATE TABLE IF NOT EXISTS `posts` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
                `title` VARCHAR(500) NOT NULL,
                `slug` VARCHAR(500) NULL DEFAULT NULL,
                `content` LONGTEXT NULL DEFAULT NULL,
                `excerpt` TEXT NULL DEFAULT NULL,
                `status` ENUM('published', 'draft', 'scheduled', 'private') DEFAULT 'draft',
                `category` VARCHAR(100) NULL DEFAULT NULL,
                `tags` TEXT NULL DEFAULT NULL,
                `featured_image` VARCHAR(500) NULL DEFAULT NULL,
                `view_count` INT(11) DEFAULT 0,
                `comment_count` INT(11) DEFAULT 0,
                `published_at` INT(11) UNSIGNED NULL DEFAULT NULL,
                `created_at` INT(11) UNSIGNED NOT NULL,
                `updated_at` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_slug` (`slug`),
                KEY `idx_user_id` (`user_id`),
                KEY `idx_status` (`status`),
                KEY `idx_category` (`category`),
                KEY `idx_published_at` (`published_at`),
                FULLTEXT KEY `fulltext_title_content` (`title`, `content`),
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->exec($createPostsTable);
        echo "Posts table created successfully.\n";

        // Create comments table
        echo "Creating comments table...\n";
        $createCommentsTable = "
            CREATE TABLE IF NOT EXISTS `comments` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `post_id` INT(11) UNSIGNED NOT NULL,
                `user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
                `parent_id` INT(11) UNSIGNED NULL DEFAULT NULL,
                `content` TEXT NOT NULL,
                `author_name` VARCHAR(100) NULL DEFAULT NULL,
                `author_ip` VARCHAR(45) NULL DEFAULT NULL,
                `status` ENUM('approved', 'pending', 'spam', 'trash') DEFAULT 'pending',
                `created_at` INT(11) UNSIGNED NOT NULL,
                `updated_at` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_post_id` (`post_id`),
                KEY `idx_user_id` (`user_id`),
                KEY `idx_parent_id` (`parent_id`),
                KEY `idx_status` (`status`),
                KEY `idx_created_at` (`created_at`),
                FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
                FOREIGN KEY (`parent_id`) REFERENCES `comments`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->exec($createCommentsTable);
        echo "Comments table created successfully.\n";

        // Create categories table
        echo "Creating categories table...\n";
        $createCategoriesTable = "
            CREATE TABLE IF NOT EXISTS `categories` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                `slug` VARCHAR(100) NOT NULL,
                `description` TEXT NULL DEFAULT NULL,
                `parent_id` INT(11) UNSIGNED NULL DEFAULT NULL,
                `order_priority` INT(11) DEFAULT 0,
                `post_count` INT(11) DEFAULT 0,
                `created_at` INT(11) UNSIGNED NOT NULL,
                `updated_at` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_slug` (`slug`),
                KEY `idx_parent_id` (`parent_id`),
                KEY `idx_order_priority` (`order_priority`),
                FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->exec($createCategoriesTable);
        echo "Categories table created successfully.\n";

        // Create settings table
        echo "Creating settings table...\n";
        $createSettingsTable = "
            CREATE TABLE IF NOT EXISTS `settings` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `setting_key` VARCHAR(100) NOT NULL,
                `setting_value` TEXT NULL DEFAULT NULL,
                `setting_type` ENUM('string', 'integer', 'boolean', 'json', 'array') DEFAULT 'string',
                `description` TEXT NULL DEFAULT NULL,
                `is_public` BOOLEAN DEFAULT FALSE,
                `created_at` INT(11) UNSIGNED NOT NULL,
                `updated_at` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_setting_key` (`setting_key`),
                KEY `idx_is_public` (`is_public`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->exec($createSettingsTable);
        echo "Settings table created successfully.\n";

        // Create media table
        echo "Creating media table...\n";
        $createMediaTable = "
            CREATE TABLE IF NOT EXISTS `media` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
                `filename` VARCHAR(255) NOT NULL,
                `original_name` VARCHAR(255) NOT NULL,
                `file_path` VARCHAR(500) NOT NULL,
                `file_url` VARCHAR(500) NOT NULL,
                `file_type` VARCHAR(100) NOT NULL,
                `file_size` BIGINT NOT NULL,
                `mime_type` VARCHAR(100) NOT NULL,
                `width` INT(11) NULL DEFAULT NULL,
                `height` INT(11) NULL DEFAULT NULL,
                `alt_text` VARCHAR(500) NULL DEFAULT NULL,
                `caption` TEXT NULL DEFAULT NULL,
                `created_at` INT(11) UNSIGNED NOT NULL,
                `updated_at` INT(11) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_user_id` (`user_id`),
                KEY `idx_file_type` (`file_type`),
                KEY `idx_created_at` (`created_at`),
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->exec($createMediaTable);
        echo "Media table created successfully.\n";

        // Insert default settings
        echo "Inserting default settings...\n";
        $currentTime = time();
        $defaultSettings = [
            ['site_name', 'SONUB', 'string', 'Website name', true, $currentTime, $currentTime],
            ['site_description', 'SONUB Website', 'string', 'Website description', true, $currentTime, $currentTime],
            ['site_url', 'http://localhost', 'string', 'Website URL', true, $currentTime, $currentTime],
            ['site_email', 'admin@sonub.com', 'string', 'Site administrator email', false, $currentTime, $currentTime],
            ['timezone', 'Asia/Seoul', 'string', 'Default timezone', true, $currentTime, $currentTime],
            ['date_format', 'Y-m-d', 'string', 'Date display format', true, $currentTime, $currentTime],
            ['time_format', 'H:i:s', 'string', 'Time display format', true, $currentTime, $currentTime],
            ['posts_per_page', '10', 'integer', 'Number of posts per page', true, $currentTime, $currentTime],
            ['allow_registration', '1', 'boolean', 'Allow user registration', true, $currentTime, $currentTime],
            ['require_phone_verification', '1', 'boolean', 'Require phone verification for new users', false, $currentTime, $currentTime],
            ['maintenance_mode', '0', 'boolean', 'Enable maintenance mode', false, $currentTime, $currentTime],
        ];

        $stmt = $db->prepare("
            INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `is_public`, `created_at`, `updated_at`)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
        echo "Default settings inserted successfully.\n";

        // Insert sample test user
        echo "Creating sample test user...\n";
        $currentTime = time();
        $stmt = $db->prepare("
            INSERT IGNORE INTO `users` (`firebase_uid`, `phone_number`, `username`, `display_name`, `status`, `created_at`, `updated_at`)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'test_firebase_uid_12345',
            '+821012345678',
            'testuser',
            'Test User',
            'active',
            $currentTime,
            $currentTime
        ]);
        echo "Sample test user created (Firebase UID: test_firebase_uid_12345, Phone: +821012345678)\n";

        echo "\n✅ Database initialization completed successfully!\n";

    } catch (PDOException $e) {
        echo "\n❌ Database initialization failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Run initialization if executed directly
if (php_sapi_name() === 'cli' && basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    init_database();
}