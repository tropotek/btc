-- --------------------------------------------
-- @version 3.0.38
--
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------

-- DB cleanup
DROP TABLE _user_role;
DROP TABLE _user_role_id;
DROP TABLE _user_role_permission;

-- ----------------------------
--
-- ----------------------------
CREATE TABLE IF NOT EXISTS `market` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `exchange_id` INT UNSIGNED NOT NULL DEFAULT 0,    -- 0 mean no live price updates
  `name` VARCHAR(128) NOT NULL DEFAULT '',
  `symbol` VARCHAR(16) NOT NULL DEFAULT '',
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `notes` TEXT,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `modified` TIMESTAMP NOT NULL,
  `created` TIMESTAMP NOT NULL,
  KEY (exchange_id),
  KEY (symbol)
) ENGINE = InnoDB;

TRUNCATE market;
INSERT INTO market (id, exchange_id, name, symbol, image, notes, modified, created) VALUES (NULL, 1, 'Bitcoin', 'BTC', '', '', NOW(), NOW());
INSERT INTO market (id, exchange_id, name, symbol, image, notes, modified, created) VALUES (NULL, 1, 'Etherium', 'ETH', '', '',NOW(), NOW());
INSERT INTO market (id, exchange_id, name, symbol, image, notes, modified, created) VALUES (NULL, 2, 'Cardano', 'ADA', '', '', NOW(), NOW());
INSERT INTO market (id, exchange_id, name, symbol, image, notes, modified, created) VALUES (NULL, 0, 'Redcoin', 'RDD', '', '', NOW(), NOW());
INSERT INTO market (id, exchange_id, name, symbol, image, notes, modified, created) VALUES (NULL, 0, 'Uniswap', 'UNI', '', '', NOW(), NOW());
INSERT INTO market (id, exchange_id, name, symbol, image, notes, modified, created) VALUES (NULL, 0, 'Einsteinium', 'EMC2', '', '', NOW(), NOW());

-- ----------------------------
-- Save the last valid exchange
--   lookup for a market record
-- Use this table to fin the best exchange to find values,
--   if none then start looping through all exchanges
-- ----------------------------
# CREATE TABLE IF NOT EXISTS `market_exchange` (
#   `market_id` INT UNSIGNED NOT NULL DEFAULT 0,
#   `exchange_id` INT UNSIGNED NOT NULL DEFAULT 0,
#   `date` TIMESTAMP NOT NULL,
#   PRIMARY KEY (market_id, exchange_id)
# ) ENGINE = InnoDB;


-- ----------------------------
--
-- ----------------------------
CREATE TABLE IF NOT EXISTS `asset` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `market_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `category_id` INT UNSIGNED NOT NULL DEFAULT 0,            -- For future wallets/collections
  `units` VARCHAR(32) NOT NULL DEFAULT '0',                       -- total units of asset owned
  `notes` TEXT,
  `modified` TIMESTAMP NOT NULL,
  `created` TIMESTAMP NOT NULL,

  KEY (`user_id`)
) ENGINE = InnoDB;

# INSERT INTO asset (id, market_id, user_id, category_id, units, notes, modified, created) VALUES (1, 3, 2, 0, '3019.739179', 'Exodus', '2021-10-15 19:00:29', '2021-10-15 18:59:40');
# INSERT INTO asset (id, market_id, user_id, category_id, units, notes, modified, created) VALUES (2, 1, 2, 0, '0.15321209', 'Exodus', '2021-10-15 19:01:12', '2021-10-15 19:01:12');
# INSERT INTO asset (id, market_id, user_id, category_id, units, notes, modified, created) VALUES (3, 2, 2, 0, '0.31440027', 'BTC Markets', '2021-10-15 19:02:36', '2021-10-15 19:02:36');
# INSERT INTO asset (id, market_id, user_id, category_id, units, notes, modified, created) VALUES (4, 3, 2, 0, '31.98942602', 'Coin Spot', '2021-10-15 19:03:12', '2021-10-15 19:03:12');
# INSERT INTO asset (id, market_id, user_id, category_id, units, notes, modified, created) VALUES (5, 5, 2, 0, '3.08451573', 'Coin Spot', '2021-10-15 19:03:47', '2021-10-15 19:03:47');
# INSERT INTO asset (id, market_id, user_id, category_id, units, notes, modified, created) VALUES (6, 4, 2, 0, '64573.15932004', 'Coin Spot', '2021-10-15 19:04:27', '2021-10-15 19:04:27');
# INSERT INTO asset (id, market_id, user_id, category_id, units, notes, modified, created) VALUES (7, 6, 2, 0, '1432.64423145', 'Coin Spot', '2021-10-15 19:04:55', '2021-10-15 19:04:55');


-- --------------------------------------------------------
--
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `asset_tick` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `asset_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `units` VARCHAR(32) NOT NULL DEFAULT '0',                       -- current total units owned
  `currency` VARCHAR(16) NOT NULL DEFAULT 'AUD',            -- EG: AUD, USD, BTC, ETH, etc
  `bid` VARCHAR(32) NOT NULL DEFAULT '0',                       -- The current value in currency terms
  `ask` VARCHAR(32) NOT NULL DEFAULT '0',                       -- The current value in currency terms
  `created` DATETIME NOT NULL,
  KEY (`asset_id`, `currency`)
) ENGINE = InnoDB;



# alter table exchange add `default` TINYINT(1) default 0 not null after `user_id`;
# UPDATE exchange SET `default` = 1 WHERE id = 1;

-- drop on release
DROP TABLE candle;
DROP TABLE equity_total;
DROP TABLE tick;






