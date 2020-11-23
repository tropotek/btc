-- --------------------------------------------
-- @version 3.0.14
--
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------


alter table tick drop column `close`;
alter table tick drop column `low`;
alter table tick add base_volume float default 0 null after last;
alter table tick add quote_volume float default 0 null after base_volume;


-- Undo (Woops)
-- alter table tick add close float default 0 null after last;

alter table tick modify base_volume DECIMAL(16,8) default 0.0 null;
alter table tick modify quote_volume DECIMAL(16,8) default 0.0 null;

