CREATE TABLE `cutts`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `release_date` DATE NOT NULL,
    `related_order_id` BIGINT NOT NULL,
    `quantity` INT NOT NULL,
    `maximum_batch_size` INT NOT NULL,
    `printed` BOOLEAN NOT NULL,
    `cutted` INT NOT NULL,
    `description` LONGTEXT NOT NULL,
    `status` TINYTEXT NOT NULL,
    `created_at` DATE NOT NULL,
    `updated_at` DATE NOT NULL,
    `created_by` TEXT NOT NULL
);
CREATE TABLE `orders`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `notification_date` DATE NOT NULL COMMENT 'تاریخی که سفارش به واحد تولید اعلام میشود',
    `product_id` BIGINT NOT NULL,
    `quantity` INT NOT NULL,
    `mount_of_order` DATE NOT NULL,
    `description` LONGTEXT NOT NULL,
    `status` TINYTEXT NOT NULL,
    `created_at` DATE NOT NULL,
    `updated_at` DATE NOT NULL,
    `created_by` TEXT NOT NULL
);
CREATE TABLE `deadlines`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT NOT NULL,
    `quantity` BIGINT NOT NULL,
    `due_date` DATE NOT NULL,
    `status` TINYTEXT NOT NULL,
    `created_at` DATE NOT NULL,
    `updated_at` DATE NOT NULL,
    `created_by` TEXT NOT NULL
);
CREATE TABLE `deliveries`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `date` DATE NOT NULL,
    `product_id` BIGINT NOT NULL,
    `quantity` INT NOT NULL,
    `description` LONGTEXT NOT NULL,
    `status` TINYTEXT NOT NULL,
    `created_at` DATE NOT NULL,
    `updated_at` DATE NOT NULL,
    `created_by` TEXT NOT NULL
);
CREATE TABLE `batches`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `release_date` DATE NOT NULL COMMENT 'بجز در بچهای بازکاری و المثنی، همیشه مساوی تاریخ دستور برش',
    `quantity` INT NOT NULL,
    `cut_id` BIGINT NOT NULL,
    `status` TINYTEXT NOT NULL,
    `created_at` DATE NOT NULL,
    `updated_at` DATE NOT NULL,
    `created_by` TEXT NOT NULL
);
CREATE TABLE `order_fulfillments`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `delivery_id` INT NOT NULL,
    `order_id` BIGINT NOT NULL,
    `fulfillment_quantity` BIGINT NOT NULL
);
CREATE TABLE `worksheets`(
    `id` BIGINT NOT NULL,
    `person_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `date` DATE NOT NULL,
    `batch_id` INT NOT NULL,
    `activity` TINYTEXT NOT NULL,
    `product_id` BIGINT NOT NULL,
    `quantity` INT NOT NULL,
    `status` TINYTEXT NOT NULL,
    `crated_at` DATE NOT NULL,
    `updated_at` DATE NOT NULL,
    `created_by` TINYINT NOT NULL,
    PRIMARY KEY(`id`)
);
CREATE TABLE `cycle_times`(
    `product_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `activity1` TINYTEXT NOT NULL,
    `activity2` TINYTEXT NOT NULL,
    `activity3` TINYTEXT NOT NULL,
    `activity4` TINYTEXT NOT NULL,
    `activity5` TINYTEXT NOT NULL,
    `activity6` TINYTEXT NOT NULL,
    `activity7` TINYTEXT NOT NULL,
    `activity8` TINYTEXT NOT NULL,
    `activity9` TINYTEXT NOT NULL,
    `activity10` TINYTEXT NOT NULL
);
CREATE TABLE `employees`(
    `id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT NOT NULL,
    `gender` TINYTEXT NOT NULL,
    `firstname` TINYTEXT NOT NULL,
    `lastname` TINYTEXT NOT NULL,
    `department` TINYTEXT NOT NULL,
    `status` TINYTEXT NOT NULL,
    PRIMARY KEY(`id`)
);
CREATE TABLE `efficiencies`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `date` DATE NOT NULL,
    `employee_id` INT UNSIGNED NOT NULL,
    `efficiency` INT UNSIGNED NOT NULL,
    `description` LONGTEXT NOT NULL
);
CREATE TABLE `users`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `gender` BIGINT NOT NULL,
    `first_name` BIGINT NOT NULL,
    `last_name` BIGINT NOT NULL,
    `email` BIGINT NOT NULL,
    `password` BIGINT NOT NULL,
    `email` BIGINT NOT NULL
);
CREATE TABLE `roles`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` BIGINT NOT NULL
);
CREATE TABLE `role_user`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role_id` BIGINT NOT NULL,
    `user_id` BIGINT NOT NULL
);
CREATE TABLE `phones`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `zone_number` BIGINT NOT NULL,
    `number` BIGINT NOT NULL,
    `type` BIGINT NOT NULL,
    `is_main` BIGINT NOT NULL
);
CREATE TABLE `customers`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `name` BIGINT NOT NULL
);
ALTER TABLE
    `efficiencies` ADD CONSTRAINT `efficiencies_employee_id_foreign` FOREIGN KEY(`employee_id`) REFERENCES `employees`(`id`);
ALTER TABLE
    `worksheets` ADD CONSTRAINT `worksheets_person_id_foreign` FOREIGN KEY(`person_id`) REFERENCES `employees`(`id`);
ALTER TABLE
    `order_fulfillments` ADD CONSTRAINT `order_fulfillments_delivery_id_foreign` FOREIGN KEY(`delivery_id`) REFERENCES `deliveries`(`id`);
ALTER TABLE
    `batches` ADD CONSTRAINT `batches_cut_id_foreign` FOREIGN KEY(`cut_id`) REFERENCES `cutts`(`id`);
ALTER TABLE
    `cutts` ADD CONSTRAINT `cutts_related_order_id_foreign` FOREIGN KEY(`related_order_id`) REFERENCES `orders`(`id`);
ALTER TABLE
    `phones` ADD CONSTRAINT `phones_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `employees` ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `order_fulfillments` ADD CONSTRAINT `order_fulfillments_order_id_foreign` FOREIGN KEY(`order_id`) REFERENCES `orders`(`id`);
ALTER TABLE
    `role_user` ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `deadlines` ADD CONSTRAINT `deadlines_order_id_foreign` FOREIGN KEY(`order_id`) REFERENCES `orders`(`id`);
ALTER TABLE
    `customers` ADD CONSTRAINT `customers_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `role_user` ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY(`role_id`) REFERENCES `roles`(`id`);
ALTER TABLE
    `worksheets` ADD CONSTRAINT `worksheets_batch_id_foreign` FOREIGN KEY(`batch_id`) REFERENCES `batches`(`id`);