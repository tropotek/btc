-- --------------------------------------------
-- @version 3.2.0
--
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------

-- TODO: Run this before upgrade
/*
rename table data to _data;
rename table plugin to _plugin;
rename table plugin_zone to _plugin_zone;
rename table migration to _migration;
*/


INSERT INTO user (type, username, title, name_first, name_last, email, phone, credentials, position, image, notes, active, last_login, session_id, password, hash, del, modified, created)
VALUES ('member', 'godar', '', 'Godar', 'Smith', 'info@tropotek.com.au', '0401999999', 'Bsci', 'Web Developer', '', '', 1, null, '', '5f4dcc3b5aa765d61d8327deb882cf99', '766702ce5e24c3ff81f27dbcfc1101a1', 0, NOW(), NOW());

UPDATE exchange t SET t.user_id = 2 WHERE t.id = 1;



-- --------------------------------
--
-- --------------------------------
CREATE TABLE IF NOT EXISTS `tick` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `exchange_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `symbol` VARCHAR(16) NOT NULL DEFAULT '',
  `timestamp` VARCHAR(32) NOT NULL DEFAULT '',
  `datetime` DATETIME NOT NULL,
  `high` FLOAT UNSIGNED DEFAULT 0.0,
  `low` FLOAT UNSIGNED DEFAULT 0.0,
  `bid` FLOAT UNSIGNED DEFAULT 0.0,
  `ask` FLOAT UNSIGNED DEFAULT 0.0,
  `last` FLOAT UNSIGNED DEFAULT 0.0,
  `close` FLOAT UNSIGNED DEFAULT 0.0,
  `change` FLOAT DEFAULT 0.0,
  `percentage` FLOAT(2) DEFAULT 0.0,

  KEY (`exchange_id`, `symbol`, `timestamp`),
  KEY (`exchange_id`, `symbol`)
) ENGINE = InnoDB;






