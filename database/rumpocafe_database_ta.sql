-- =============================================================================
-- SKEMA BASIS DATA SISTEM POS & ORDERING RUMPO CAFE (UNTUK TUGAS AKHIR / SKRIPSI)
-- Standarisasi Primary Key Eksplisit (Contoh: order_id, menu_id, user_id, dsb.)
-- Database: MySQL 8.0+ / MariaDB 10.4+
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. TABEL: users (Manajemen Pengguna Sistem)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `user_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'cashier', 'kitchen', 'marketing') NOT NULL DEFAULT 'cashier',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. TABEL: categories (Kategori Menu Makanan & Minuman)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `category_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. TABEL: menus (Daftar Menu Makanan, Minuman, & Camilan)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
    `menu_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `price` DECIMAL(12, 2) NOT NULL,
    `image` VARCHAR(255) NULL DEFAULT NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `is_best_seller` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_menus_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. TABEL: tables (Daftar Meja & QR Code Dine-In)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tables`;
CREATE TABLE `tables` (
    `table_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `table_number` VARCHAR(50) NOT NULL UNIQUE,
    `status` ENUM('available', 'occupied', 'reserved') NOT NULL DEFAULT 'available',
    `qr_code` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. TABEL: customers (Data Pelanggan & Loyalty Points)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
    `customer_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NULL DEFAULT NULL,
    `phone` VARCHAR(30) NOT NULL UNIQUE,
    `points` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. TABEL: promotions (Voucher Kode Diskon Marketing)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions` (
    `promotion_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `discount_percentage` DECIMAL(5, 2) NOT NULL,
    `max_discount` DECIMAL(12, 2) NULL DEFAULT NULL,
    `max_uses` INT NULL DEFAULT NULL,
    `uses_count` INT NOT NULL DEFAULT 0,
    `start_date` TIMESTAMP NULL DEFAULT NULL,
    `end_date` TIMESTAMP NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. TABEL: event_promotions (Promo Event & Banner Musiman)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `event_promotions`;
CREATE TABLE `event_promotions` (
    `event_promotion_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `theme` VARCHAR(100) NOT NULL DEFAULT 'general',
    `headline` VARCHAR(255) NULL DEFAULT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `banner_image` VARCHAR(255) NULL DEFAULT NULL,
    `coupon_code` VARCHAR(50) NULL DEFAULT NULL,
    `discount_percentage` DECIMAL(5, 2) NOT NULL DEFAULT 0,
    `usage_limit` INT NULL DEFAULT NULL,
    `start_date` TIMESTAMP NULL DEFAULT NULL,
    `end_date` TIMESTAMP NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. TABEL: bundles (Menu Paket Hemat / Bundling)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `bundles`;
CREATE TABLE `bundles` (
    `bundle_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `price` DECIMAL(12, 2) NOT NULL,
    `image` VARCHAR(255) NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. TABEL: bundle_items (Relasi Item Menu dalam Paket Bundling)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `bundle_items`;
CREATE TABLE `bundle_items` (
    `bundle_item_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `bundle_id` BIGINT UNSIGNED NOT NULL,
    `menu_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bundle_items_bundle` FOREIGN KEY (`bundle_id`) REFERENCES `bundles` (`bundle_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bundle_items_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`menu_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. TABEL: orders (Data Transaksi Penjualan / Pesanan)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
    `order_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `table_id` BIGINT UNSIGNED NOT NULL,
    `customer_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `customer_name` VARCHAR(255) NOT NULL,
    `customer_phone` VARCHAR(30) NULL DEFAULT NULL,
    `total_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `points_earned` INT NOT NULL DEFAULT 0,
    `points_redeemed` INT NOT NULL DEFAULT 0,
    `promotion_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `status` ENUM('waiting_payment', 'waiting_verification', 'verified', 'cooking', 'ready', 'completed', 'cancelled') NOT NULL DEFAULT 'waiting_payment',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_table` FOREIGN KEY (`table_id`) REFERENCES `tables` (`table_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_orders_promotion` FOREIGN KEY (`promotion_id`) REFERENCES `event_promotions` (`event_promotion_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. TABEL: order_details (Rincian Item yang Dipesan)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `order_details`;
CREATE TABLE `order_details` (
    `order_detail_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `menu_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `bundle_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `notes` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_order_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_details_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`menu_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_order_details_bundle` FOREIGN KEY (`bundle_id`) REFERENCES `bundles` (`bundle_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 12. TABEL: payments (Transaksi Pembayaran & Verifikasi Kasir)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
    `payment_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL DEFAULT 'qris',
    `proof_image` VARCHAR(255) NULL DEFAULT NULL,
    `status` ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 13. TABEL: qris (Konfigurasi Barcode QRIS Toko)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `qris`;
CREATE TABLE `qris` (
    `qris_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `image_path` VARCHAR(255) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 14. TABEL: settings (Pengaturan Global Struk & Identitas Kafe)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `setting_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 15. TABEL: sessions (Sesi Pengguna Framework)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `session_id` VARCHAR(255) NOT NULL PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` TEXT NULL DEFAULT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `idx_sessions_user` (`user_id`),
    INDEX `idx_sessions_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 16. TABEL: password_reset_tokens (Token Reset Kata Sandi)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
