-- Legacy products import helper for this project schema.
-- Target schema (products): id, category_id, brand_id, name, slug, description, price, image, stock, timestamps, deleted_at
-- Source schema (legacy): 42-column products dump with fields like short_name, thumb_image, long_description, qty, status, etc.
--
-- How to use:
-- 1) Run this file up to (and including) tmp table creation.
-- 2) Paste your legacy INSERT block in place of the marker below.
-- 3) IMPORTANT: Change table name in pasted SQL from `products` to `tmp_products_import`.
--    Example:
--    INSERT INTO `products` (...) VALUES (...);
--    becomes
--    INSERT INTO `tmp_products_import` (...) VALUES (...);
-- 4) Run the rest of this file.
--
-- Notes:
-- - Slugs are normalized and prefixed with `p{id}-` to guarantee uniqueness in this app.
-- - `description` uses `long_description`, fallback `short_description`.
-- - `price` uses `offer_price` when present, otherwise `price`.
-- - `image` uses `thumb_image`, fallback `banner_image`.
-- - `stock` maps from `qty`.
-- - `status=0` maps to soft-deleted rows (`deleted_at` set).

SET NAMES utf8mb4;
SET @OLD_SQL_SAFE_UPDATES := @@SQL_SAFE_UPDATES;
SET SQL_SAFE_UPDATES = 0;

START TRANSACTION;

DROP TABLE IF EXISTS `tmp_products_import`;
CREATE TABLE `tmp_products_import` (
  `id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `short_name` VARCHAR(255) NULL,
  `slug` VARCHAR(255) NULL,
  `thumb_image` VARCHAR(1024) NULL,
  `banner_image` VARCHAR(1024) NULL,
  `vendor_id` BIGINT NULL,
  `category_id` BIGINT NULL,
  `sub_category_id` BIGINT NULL,
  `child_category_id` BIGINT NULL,
  `brand_id` BIGINT NULL,
  `qty` INT NULL,
  `short_description` LONGTEXT NULL,
  `long_description` LONGTEXT NULL,
  `video_link` TEXT NULL,
  `sku` VARCHAR(255) NULL,
  `seo_title` VARCHAR(255) NULL,
  `seo_description` TEXT NULL,
  `price` DECIMAL(12,2) NULL,
  `offer_price` DECIMAL(12,2) NULL,
  `offer_start_date` DATETIME NULL,
  `offer_end_date` DATETIME NULL,
  `tax_id` BIGINT NULL,
  `is_cash_delivery` TINYINT(1) NULL,
  `is_return` TINYINT(1) NULL,
  `return_policy_id` BIGINT NULL,
  `tags` TEXT NULL,
  `is_warranty` TINYINT(1) NULL,
  `show_homepage` TINYINT(1) NULL,
  `is_undefine` TINYINT(1) NULL,
  `is_featured` TINYINT(1) NULL,
  `is_wholesale` TINYINT(1) NULL,
  `new_product` TINYINT(1) NULL,
  `is_top` TINYINT(1) NULL,
  `is_best` TINYINT(1) NULL,
  `is_flash_deal` TINYINT(1) NULL,
  `flash_deal_date` DATETIME NULL,
  `buyone_getone` TINYINT(1) NULL,
  `status` TINYINT(1) NULL,
  `is_specification` TINYINT(1) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================
-- PASTE LEGACY INSERTS HERE
-- ==========================
-- Make sure pasted statements insert into `tmp_products_import` (not `products`).
-- Example:
-- INSERT INTO `tmp_products_import` (`id`, `name`, ... , `updated_at`) VALUES (...);

-- ==========================
-- END LEGACY INSERT SECTION
-- ==========================

-- Import into current `products` table.
INSERT INTO `products`
  (`id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `price`, `image`, `stock`, `created_at`, `updated_at`, `deleted_at`)
SELECT
  t.`id`,
  COALESCE(c.`id`, 1) AS `category_id`,
  b.`id` AS `brand_id`,
  t.`name`,
  LEFT(
    CONCAT(
      'p', t.`id`, '-',
      TRIM(BOTH '-' FROM
        LOWER(
          REPLACE(
            REPLACE(
              REPLACE(
                REPLACE(
                  COALESCE(NULLIF(TRIM(t.`slug`), ''), TRIM(t.`name`)),
                  '&', ' and '
                ),
                '''', ''
              ),
              ' ', '-'
            ),
            '/', '-'
          )
        )
      )
    ),
    255
  ) AS `slug`,
  COALESCE(NULLIF(t.`long_description`, ''), NULLIF(t.`short_description`, '')) AS `description`,
  CAST(COALESCE(NULLIF(t.`offer_price`, 0), t.`price`, 0) AS DECIMAL(10,2)) AS `price`,
  COALESCE(NULLIF(TRIM(t.`thumb_image`), ''), NULLIF(TRIM(t.`banner_image`), '')) AS `image`,
  GREATEST(COALESCE(t.`qty`, 0), 0) AS `stock`,
  COALESCE(t.`created_at`, NOW()) AS `created_at`,
  COALESCE(t.`updated_at`, NOW()) AS `updated_at`,
  CASE
    WHEN COALESCE(t.`status`, 1) = 1 THEN NULL
    ELSE COALESCE(t.`updated_at`, NOW())
  END AS `deleted_at`
FROM `tmp_products_import` t
LEFT JOIN `categories` c ON c.`id` = t.`category_id`
LEFT JOIN `brands` b ON b.`id` = NULLIF(t.`brand_id`, 0)
WHERE t.`name` IS NOT NULL
  AND TRIM(t.`name`) <> ''
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `brand_id` = VALUES(`brand_id`),
  `name` = VALUES(`name`),
  `slug` = VALUES(`slug`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `image` = VALUES(`image`),
  `stock` = VALUES(`stock`),
  `updated_at` = VALUES(`updated_at`),
  `deleted_at` = VALUES(`deleted_at`);

-- Optional: store banner image as additional gallery image.
INSERT INTO `product_images` (`product_id`, `path`, `created_at`, `updated_at`)
SELECT
  p.`id`,
  t.`banner_image`,
  COALESCE(t.`created_at`, NOW()),
  COALESCE(t.`updated_at`, NOW())
FROM `tmp_products_import` t
JOIN `products` p ON p.`id` = t.`id`
LEFT JOIN `product_images` pi
  ON pi.`product_id` = p.`id`
 AND pi.`path` = t.`banner_image`
WHERE t.`banner_image` IS NOT NULL
  AND TRIM(t.`banner_image`) <> ''
  AND (t.`thumb_image` IS NULL OR t.`banner_image` <> t.`thumb_image`)
  AND pi.`id` IS NULL;

DROP TABLE IF EXISTS `tmp_products_import`;

COMMIT;

SET SQL_SAFE_UPDATES = @OLD_SQL_SAFE_UPDATES;
