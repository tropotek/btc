

TRUNCATE `user`;
INSERT INTO `user` (`role_id`, `name`, `email`, `username`, `password`, `hash`, `modified`, `created`)
VALUES
  (1, 'Administrator', 'admin@example.com', 'admin', MD5('password'), MD5('1admin'), NOW() , NOW())
;

-- ------------------------------------------------
--   BTC App DB
--
-- ------------------------------------------------








