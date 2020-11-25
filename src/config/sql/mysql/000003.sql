-- --------------------------------------------
-- @version 3.0.22
--
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------


--  OHLCV candles
--  1504541580000, // UTC timestamp in milliseconds, integer
--  4235.4,        // (O)pen price, float
--  4240.6,        // (H)ighest price, float
--  4230.0,        // (L)owest price, float
--  4230.7,        // (C)losing price, float
--  37.72941911    // (V)olume (in terms of the base currency), float

-- ----------------------------
--
-- ----------------------------
CREATE TABLE IF NOT EXISTS `candle` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  -- These are the real primary keys
  `exchange_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `symbol` VARCHAR(16) NOT NULL DEFAULT '',
  `period` VARCHAR(1) NOT NULL DEFAULT '',        -- The period unit for a single candle[t=minute h=hour, d=day, m=month]
  -- `timestamp` timestamp NOT NULL,
  `timestamp` INT UNSIGNED DEFAULT 0,

  `open` FLOAT UNSIGNED DEFAULT 0.0,
  `high` FLOAT UNSIGNED DEFAULT 0.0,
  `low` FLOAT UNSIGNED DEFAULT 0.0,
  `close` FLOAT UNSIGNED DEFAULT 0.0,
  `volume` FLOAT UNSIGNED DEFAULT 0.0,

  -- `created` TIMESTAMP NOT NULL,

  UNIQUE KEY (exchange_id, symbol, period, timestamp),
  KEY (`exchange_id`)
) ENGINE = InnoDB;



