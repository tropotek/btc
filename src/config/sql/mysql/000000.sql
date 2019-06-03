

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
    `market` VARCHAR(8) NOT NULL DEFAULT 'ALL',          -- EG: BTC, XRP, ALL = 'All available market totals'
    `currency` VARCHAR(8) NOT NULL DEFAULT 'AUD',        -- EG: AUD, USD, BTC, ETH, etc
    `amount` VARCHAR(128) NOT NULL DEFAULT '0',
    `created` DATETIME NOT NULL,
    PRIMARY KEY (`exchange_id`, `market`, `currency`, `created`)
) ENGINE = InnoDB;

-- Edit existing, remove for new installs
# alter table equity_total add market VARCHAR(8) default 'ALL' not null after exchange_id;
# alter table equity_total modify currency varchar(8) default 'AUD' not null;
# alter table equity_total drop primary key;
# alter table equity_total add primary key (exchange_id, market, currency, created);
#INSERT INTO `user_permission` (`role_id`, `name`) VALUES (1, 'type.user');


DELETE FROM equity_total WHERE market = 'ETH' OR market = 'BCH';

