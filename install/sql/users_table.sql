CREATE TABLE IF NOT EXISTS `users`
  (
     `usersid`    INT(11) NOT NULL auto_increment,
     `uid`        VARCHAR(255) NOT NULL,
     `username`   VARCHAR(255) NOT NULL,
     `password`   VARCHAR(255) NOT NULL,
     `email`      VARCHAR(255) NOT NULL,
     `website`    VARCHAR(255) NOT NULL,
     `location`   VARCHAR(255) NOT NULL,
     `avatar`     BLOB NOT NULL,
     `join_date`  DATE NOT NULL,
     `total_post` VARCHAR(255) NOT NULL,
     `total_hits` VARCHAR(255) NOT NULL,
     PRIMARY KEY (`usersid`)
  ) 
