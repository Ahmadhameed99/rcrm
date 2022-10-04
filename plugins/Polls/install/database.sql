CREATE TABLE IF NOT EXISTS `polls`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` TEXT COLLATE utf8_unicode_ci NOT NULL,
    `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `expire_at` date NULL,
    `status` ENUM('active', 'inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
    `total_views` INT(11) NOT NULL DEFAULT '0',
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 1; --#

CREATE TABLE IF NOT EXISTS `poll_answers`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `poll_id` INT(11) NOT NULL,
    `title` TEXT NOT NULL,
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY(`id`)
) ENGINE = INNODB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8; --#

CREATE TABLE IF NOT EXISTS `poll_votes`(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `poll_id` INT(11) NOT NULL,
    `poll_answer_id` INT(11) NOT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 1; --#

CREATE TABLE IF NOT EXISTS `poll_settings`(
    `setting_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    `setting_value` MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL,
    `type` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'app',
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    UNIQUE KEY `setting_name`(`setting_name`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci; --#