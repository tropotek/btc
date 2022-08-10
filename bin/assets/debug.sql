-- ------------------------------------------------------
-- Dev setup
-- Author: Michael Mifsud
-- Date: 15/03/20
-- ------------------------------------------------------

-- --------------------------------------
-- Change all passwords to 'password' for debug mode
-- --------------------------------------
UPDATE `user` SET `hash` = MD5(CONCAT(`id`, `username`)) WHERE `hash` = '' OR `hash` IS NULL;
-- Unsalted
UPDATE `user` SET `password` = MD5('password') WHERE 1;
-- UPDATE `user` SET `password` = MD5(CONCAT('password', `hash`)) WHERE 1;

UPDATE `auth` SET `password` = 'pZqopdrSqso=' WHERE 1;
-- UPDATE `auth` SET authtool = 'pJqpmtfSp9KDYpPZptXSVGCVhnl9r425e7d/royzgnZoa2c=' WHERE authtool != '';
UPDATE `auth` SET `keys` = '' WHERE authtool != '';




