

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
  `currency` VARCHAR(8) NOT NULL DEFAULT 'AUD',
  `icon` VARCHAR(255) NOT NULL DEFAULT '',          -- CSS or image path
  `description` TEXT,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `options` TEXT,                                   -- TODO: use for extra options that an exchange would rquire (array/JSON)

  `del` BOOL NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL,
  `created` DATETIME NOT NULL,
  KEY (`user_id`)
) ENGINE = InnoDB;



-- --------------------------------------------------------
--  store snapshots of the total equity in your exchange
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `equity_total` (
    `exchange_id` INT UNSIGNED NOT NULL DEFAULT 0,
    `currency` VARCHAR(128) NOT NULL DEFAULT '',        -- EG: AUD, USD, BTC, ETH, etc
    `amount` VARCHAR(128) NOT NULL DEFAULT '',
    `created` DATETIME NOT NULL,
    PRIMARY KEY (`exchange_id`, `currency`, `created`)
) ENGINE = InnoDB;




CREATE TABLE IF NOT EXISTS `market_value` (
    `exchange_id` INT UNSIGNED NOT NULL DEFAULT 0,
    `market` VARCHAR(128) NOT NULL DEFAULT '',          -- EG: BTC, XRP
    `currency` VARCHAR(128) NOT NULL DEFAULT '',        -- EG: AUD, USD, BTC, ETH, etc
    `amount` VARCHAR(128) NOT NULL DEFAULT '',          -- The value of this market at this currency
    `created` DATETIME NOT NULL,
    PRIMARY KEY (`exchange_id`, `market`, `currency`, `created`)
) ENGINE = InnoDB;








