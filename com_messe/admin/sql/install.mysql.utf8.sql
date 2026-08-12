-- Gestione Orari Messe by Gioacchino Cipriano
-- Installazione tabelle DB

CREATE TABLE IF NOT EXISTS `#__messe_chiese` (
    `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome`         VARCHAR(255)     NOT NULL DEFAULT '',
    `descrizione`  TEXT,
    `rito`         ENUM('romano','ambrosiano') NOT NULL DEFAULT 'romano',
    `indirizzo`    VARCHAR(255)     DEFAULT NULL,
    `ora_veglia`   TINYINT(2) UNSIGNED NOT NULL DEFAULT 21,
    `minuti_veglia`TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `modalita_prefestiva` ENUM('nessuna','vigiliare','dedicato','feriale_serale') NOT NULL DEFAULT 'feriale_serale',
    `sabato_solennita` ENUM('vigiliare','festivo') NOT NULL DEFAULT 'festivo',
    `published`    TINYINT(1)       NOT NULL DEFAULT 1,
    `ordering`     INT(11)          NOT NULL DEFAULT 0,
    `created`      DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified`     DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by`   INT(11)          NOT NULL DEFAULT 0,
    `modified_by`  INT(11)          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__messe_orari` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chiesa_id`  INT(11) UNSIGNED NOT NULL,
    `tipo`       ENUM('feriale','vigilia','festivo','prefestivo') NOT NULL DEFAULT 'feriale',
    `ora`        TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `minuti`     TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `label`      VARCHAR(100) DEFAULT NULL,
    `giorni`     VARCHAR(20)  DEFAULT NULL,
    `ordering`   INT(11)      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_chiesa_tipo` (`chiesa_id`, `tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__messe_eccezioni` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chiesa_id`  INT(11) UNSIGNED NOT NULL,
    `data_md`    CHAR(5)      NOT NULL DEFAULT '',
    `ora`        TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `minuti`     TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `label`      VARCHAR(255) NOT NULL DEFAULT '',
    `luogo`      VARCHAR(255) DEFAULT NULL,
    `modalita`   ENUM('sostituisci','aggiungi') NOT NULL DEFAULT 'sostituisci',
    `published`  TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_chiesa_data` (`chiesa_id`, `data_md`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__messe_periodi` (
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chiesa_id`   INT(11) UNSIGNED NOT NULL,
    `nome`        VARCHAR(255)     NOT NULL DEFAULT '',
    `tipo_data`   ENUM('date','mesi') NOT NULL DEFAULT 'date',
    `data_inizio` CHAR(5)  DEFAULT NULL COMMENT 'MM-GG per tipo_data=date',
    `data_fine`   CHAR(5)  DEFAULT NULL COMMENT 'MM-GG per tipo_data=date',
    `mesi`        VARCHAR(30) DEFAULT NULL COMMENT 'JSON es. [7,8] per tipo_data=mesi',
    `azione`      ENUM('sopprimi','sostituisci') NOT NULL DEFAULT 'sopprimi',
    `tipo_orario` ENUM('feriale','vigilia','festivo','tutti') NOT NULL DEFAULT 'feriale',
    `orari_nuovi` TEXT DEFAULT NULL COMMENT 'JSON orari sostitutivi [{h,m,label,giorni}]',
    `published`   TINYINT(1) NOT NULL DEFAULT 1,
    `note`        VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_chiesa` (`chiesa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__messe_settimana_santa` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chiesa_id`  INT(11) UNSIGNED NOT NULL,
    `giorno_riferimento` ENUM('palme','lunedi_santo','martedi_santo','mercoledi_santo','giovedi_santo','venerdi_santo','sabato_santo') NOT NULL DEFAULT 'palme',
    `ora`        TINYINT(2) UNSIGNED NOT NULL DEFAULT 18,
    `minuti`     TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `label`      VARCHAR(255) NOT NULL DEFAULT '',
    `luogo`      VARCHAR(255) DEFAULT NULL,
    `modalita`   ENUM('sostituisci','aggiungi') NOT NULL DEFAULT 'aggiungi',
    `published`  TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_chiesa_giorno` (`chiesa_id`, `giorno_riferimento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
