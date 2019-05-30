

TRUNCATE `user`;
INSERT INTO `user` (`role_id`, `name`, `email`, `username`, `password`, `hash`, `modified`, `created`)
VALUES
  (1, 'Administrator', 'admin@example.com', 'admin', MD5('password'), MD5('1admin'), NOW() , NOW())
;

-- ------------------------------------------------
--   BTC App DB
--
-- ------------------------------------------------



-- ----------------------------
--  exchange
-- ----------------------------
CREATE TABLE IF NOT EXISTS `exchange` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `driver` VARCHAR(128) NOT NULL DEFAULT '',        -- The ccxt classname to use for this account
  `username` VARCHAR(255) NOT NULL DEFAULT '',
  `api_key` VARCHAR(128) NOT NULL DEFAULT '',       -- The public api key
  `secret` VARCHAR(128) NOT NULL DEFAULT '',        -- The private API key
  `icon` VARCHAR(128) NOT NULL DEFAULT '',
  `description` TEXT,
  `options` TEXT,                                   -- TODO: use for extra options that an exchange would rquire (array/JSON)

  `del` BOOL NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL,
  `created` DATETIME NOT NULL,
  KEY (`user_id`)
) ENGINE = InnoDB;





