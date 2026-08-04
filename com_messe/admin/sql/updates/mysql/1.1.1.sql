-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.1.1 — Crea tabella periodi stagionali se non esiste

CREATE TABLE IF NOT EXISTS `#__messe_periodi` (
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chiesa_id`   INT(11) UNSIGNED NOT NULL,
    `nome`        VARCHAR(255)     NOT NULL DEFAULT '',
    `tipo_data`   ENUM('date','mesi') NOT NULL DEFAULT 'date',
    `data_inizio` CHAR(5)          DEFAULT NULL,
    `data_fine`   CHAR(5)          DEFAULT NULL,
    `mesi`        VARCHAR(30)      DEFAULT NULL,
    `azione`      ENUM('sopprimi','sostituisci') NOT NULL DEFAULT 'sopprimi',
    `tipo_orario` ENUM('feriale','vigilia','festivo','tutti') NOT NULL DEFAULT 'feriale',
    `orari_nuovi` TEXT             DEFAULT NULL,
    `published`   TINYINT(1)       NOT NULL DEFAULT 1,
    `note`        VARCHAR(255)     DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_chiesa` (`chiesa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
