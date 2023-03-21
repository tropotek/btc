-- --------------------------------------------
-- @version 3.0.68
--
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------

ALTER TABLE asset
    ADD in_total BOOL DEFAULT TRUE NOT NULL AFTER units;

UPDATE asset_tick SET created = date_format(created, '%Y-%m-%d %H:%i:00');
