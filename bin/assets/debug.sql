-- ------------------------------------------------------
-- Dev setup
-- Author: Michael Mifsud
-- Date: 15/03/20
-- ------------------------------------------------------

-- --------------------------------------
-- Change all passwords to 'password' for debug mode
-- --------------------------------------
UPDATE `user` SET `hash` = MD5(CONCAT(`id`, IFNULL(`institution_id`, 0), `username`)) WHERE `hash` = '' OR `hash` IS NULL;
-- Unsalted
UPDATE `user` SET `password` = MD5('password') WHERE 1;
-- UPDATE `user` SET `password` = MD5(CONCAT('password', `hash`)) WHERE 1;







