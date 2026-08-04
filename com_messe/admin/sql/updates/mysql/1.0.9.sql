-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.0.9 — Crea tabelle se non esistono

CREATE TABLE IF NOT EXISTS `#__messe_chiese` (
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome`          VARCHAR(255)     NOT NULL DEFAULT '',
    `descrizione`   TEXT,
    `rito`          ENUM('romano','ambrosiano') NOT NULL DEFAULT 'romano',
    `indirizzo`     VARCHAR(255)     DEFAULT NULL,
    `ora_veglia`    TINYINT(2) UNSIGNED NOT NULL DEFAULT 21,
    `minuti_veglia` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `published`     TINYINT(1)       NOT NULL DEFAULT 1,
    `ordering`      INT(11)          NOT NULL DEFAULT 0,
    `created`       DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified`      DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by`    INT(11)          NOT NULL DEFAULT 0,
    `modified_by`   INT(11)          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__messe_orari` (
    `id`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chiesa_id` INT(11) UNSIGNED NOT NULL,
    `tipo`      ENUM('feriale','vigilia','festivo') NOT NULL DEFAULT 'feriale',
    `ora`       TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `minuti`    TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `label`     VARCHAR(100) DEFAULT NULL,
    `giorni`    VARCHAR(20)  DEFAULT NULL,
    `ordering`  INT(11)      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_chiesa_tipo` (`chiesa_id`, `tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__messe_eccezioni` (
    `id`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chiesa_id` INT(11) UNSIGNED NOT NULL,
    `data_md`   CHAR(5)          NOT NULL DEFAULT '',
    `ora`       TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `minuti`    TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `label`     VARCHAR(255)     NOT NULL DEFAULT '',
    `luogo`     VARCHAR(255)     DEFAULT NULL,
    `published` TINYINT(1)       NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_chiesa_data` (`chiesa_id`, `data_md`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
