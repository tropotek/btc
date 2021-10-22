-- --------------------------------------------
-- @version 3.0.68
--
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------


-- --------------------------------------------------------
-- Create table for storing authentication accounts
--
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `auth` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
    `name` VARCHAR(64) NOT NULL DEFAULT '',                         --
    `url` VARCHAR(128) NOT NULL DEFAULT '',                         -- The url to the website that this auth record is for
    `username` VARCHAR(128) NOT NULL DEFAULT '',                    --
    `password` VARCHAR(128) NOT NULL DEFAULT '',                    -- (encode)
    `authtool` VARCHAR(128) NOT NULL DEFAULT '',                    -- (encode) key gen for authtool EG: "oathtool --totp -b 7GJXAAAGKTRRF412"
    `keys` TEXT,                                                    -- (encode) could be a wallet key, or API key, etc
    `notes` TEXT,                                                   --
    `del` BOOL NOT NULL DEFAULT 0,
    `modified` TIMESTAMP NOT NULL,
    `created` TIMESTAMP NOT NULL,
    KEY (`user_id`)
) ENGINE = InnoDB;



