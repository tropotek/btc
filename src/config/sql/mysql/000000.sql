




TRUNCATE `user`;
INSERT INTO `user` (`role_id`, `name`, `email`, `username`, `password`, `hash`, `modified`, `created`)
VALUES
  (1, 'Administrator', 'admin@example.com', 'admin', MD5('password'), MD5('1admin'), NOW() , NOW()),
  (2, 'User', 'user@example.com', 'user1', MD5('password'), MD5('2user'), NOW() , NOW())
;
