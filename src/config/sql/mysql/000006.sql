-- --------------------------------------------
-- @version 3.0.56
--
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------

alter table asset_tick add user_id INT(10) UNSIGNED default 0 not null after id;

UPDATE `asset_tick` a, `asset` b
  SET a.`user_id` = b.`user_id`
  WHERE a.`asset_id` = b.`id`;
