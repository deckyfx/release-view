
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- configs
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `configs`;

CREATE TABLE `configs`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(255) DEFAULT '',
    `value` TEXT,
    `type` enum('string','int','double','boolean'),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- builds
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `builds`;

CREATE TABLE `builds`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) DEFAULT '',
    `version` VARCHAR(255) DEFAULT '',
    `url` TEXT,
    `note` TEXT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
