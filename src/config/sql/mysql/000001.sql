-- --------------------------------------------
-- @version install
--
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------

-- TODO: Run this befor upgrade
/*
rename table data to _data;
rename table plugin to _plugin;
rename table plugin_zone to _plugin_zone;
rename table migration to _migration;
*/


INSERT INTO dev_btc.user (type, username, title, name_first, name_last, email, phone, credentials, position, image, notes, active, last_login, session_id, password, hash, del, modified, created)
VALUES ('member', 'godar', '', 'Godar', 'Smith', 'info@tropotek.com.au', '0401999999', 'Bsci', 'Web Developer', '', '', 1, null, '', '5f4dcc3b5aa765d61d8327deb882cf99', '766702ce5e24c3ff81f27dbcfc1101a1', 0, NOW(), NOW());

UPDATE exchange t SET t.user_id = 2 WHERE t.id = 1;





