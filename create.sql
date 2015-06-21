-- Create our database
CREATE DATABASE itdept_test CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Switch to our database
USE itdept_test;

-- Create our tables
-- First up, our table that will house all of the images!
CREATE TABLE IF NOT EXISTS `smp_images` (
	`image_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`image_guid` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`image_file_path` VARCHAR(4096) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`image_thumb_path` VARCHAR(4096) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`image_title` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`image_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
	`image_status` TINYINT NOT NULL DEFAULT 2,
	`image_delete_key` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`image_hash` VARCHAR(96) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`user_id` BIGINT UNSIGNED DEFAULT NULL,
	`created_at` DATETIME DEFAULT NULL,
	`updated_at` DATETIME DEFAULT NULL,
	PRIMARY KEY(image_id),
	UNIQUE(image_guid),
	INDEX `guid_index` (`image_guid`),
	INDEX `image_delete_index` (`image_delete_key`),
	INDEX `user_id_index` (`user_id`)
);

-- Second, our table that will house our list of users
CREATE TABLE IF NOT EXISTS `smp_users` (
	`user_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_nickname` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_email` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_password_salt` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`user_password_hash` VARCHAR(96) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`created_at` DATETIME DEFAULT NULL,
	`updated_at` DATETIME DEFAULT NULL,
	PRIMARY KEY(user_id),
	UNIQUE(user_nickname, user_email),
	INDEX `nickname_index` (`user_nickname`),
	INDEX `email_index` (`user_email`)
);

-- Third, our table that will keep track of user password resets
-- We'll hardcode it to expire after 24 hours!
CREATE TABLE IF NOT EXISTS `smp_users_password_reset` (
	`reset_id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`reset_passphrase` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`user_id` BIGINT UNSIGNED DEFAULT 0 NOT NULL,
	`reset_time` DATETIME NOT NULL,
	`reset_status` TINYINT DEFAULT 1 NOT NULL,
	PRIMARY KEY(reset_id, reset_passphrase)
);

-- Fourth, our table that will keep track of the user password reset status
CREATE TABLE IF NOT EXISTS `smp_users_password_reset_status` (
	`reset_status` TINYINT AUTO_INCREMENT NOT NULL,
	`reset_status_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY(reset_status),
	UNIQUE(reset_status_name)
);
-- Default: When the reset request is created this status is set
INSERT INTO `smp_users_password_reset_status` (reset_status_name) VALUES ('Valid');
-- When the password reset request is completed or times out this status is set
INSERT INTO `smp_users_password_reset_status` (reset_status_name) VALUES ('Expired');

-- Fifth, our table that will log the events the users trigger through site actions
CREATE TABLE IF NOT EXISTS `smp_user_events` (
	`event_id` BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
	`event_status` TINYINT NOT NULL,
	`event_date` DATETIME NOT NULL,
	`image_id` BIGINT UNSIGNED DEFAULT NULL,
	`user_id` BIGINT UNSIGNED DEFAULT NULL,
	`tag_id` BIGINT UNSIGNED DEFAULT NULL,
	`user_ip_address` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`user_agent` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
	PRIMARY KEY(event_id)
);

-- Sixth, our table that houses all of the different events that can be triggered
CREATE TABLE IF NOT EXISTS `smp_event_status` (
	`event_status` TINYINT NOT NULL AUTO_INCREMENT,
	`event_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`event_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY(event_status),
	UNIQUE(event_name)
);
-- Create the events
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('ACCOUNT_CREATED', 'Account created.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('LOGIN_FAILURE', 'User login failed.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('LOGIN_SUCCESS', 'User logged in.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('LOGOUT_SUCCESS', 'User logged out.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('PASSWORD_RESET_START', 'User initiated password reset sequence.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('PASSWORD_RESET_COMPLETED', 'User reset password successfully.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('IMAGE_UPLOADED', 'User uploaded image.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('IMAGE_DELETED', 'User deleted image.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('TAG_ADDED', 'User added image tag.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('TAG_REMOVED', 'User removed image tag.');
INSERT INTO `smp_event_status` (event_name, event_description) VALUES ('IMAGE_PERMISSION_CHANGED', 'User changed image permissions.');

-- Create our image states to give users control over the accessibility of images
CREATE TABLE IF NOT EXISTS `smp_image_status` (
	`image_status` TINYINT NOT NULL AUTO_INCREMENT,
	`image_status_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY(image_status),
	UNIQUE(image_status_name)
);
-- Default: Visible to the world
INSERT INTO `smp_image_status` (image_status_name) VALUES ('Published');
-- Visible only to people with the link (or GUID)
INSERT INTO `smp_image_status` (image_status_name) VALUES ('Private');
-- Visible only to the owner of the image
INSERT INTO `smp_image_status` (image_status_name) VALUES ('Disabled');
-- Special flag, used for avatars!
INSERT INTO `smp_image_status` (image_status_name) VALUES ('Avatar');
-- Deleted permanently
INSERT INTO `smp_image_status` (image_status_name) VALUES ('Deleted');

-- Create our table for image tags
CREATE TABLE IF NOT EXISTS `smp_tags` (
	`tag_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`tag_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`tag_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`created_at` DATETIME DEFAULT NULL,
	`updated_at` DATETIME DEFAULT NULL,
	PRIMARY KEY(tag_id),
	UNIQUE(tag_name),
	INDEX `tag_name_index` (`tag_name`)
);

-- Create our table to store tags corresponding to each image
CREATE TABLE IF NOT EXISTS `smp_image_tags` (
	`image_tag_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`tag_id` BIGINT UNSIGNED NOT NULL,
	`image_id` BIGINT UNSIGNED NOT NULL,
	`created_at` DATETIME DEFAULT NULL,
	`updated_at` DATETIME DEFAULT NULL,
	PRIMARY KEY(image_tag_id),
	-- INDEX `tag_id_index` (`tag_id`),
	INDEX `image_id_index` (`image_id`)
);

-- Constraints...
-- TO DO: IF I HAVE MORE TIME