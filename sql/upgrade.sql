ALTER TABLE category ENGINE = MyISAM;
ALTER TABLE feedback ENGINE = MyISAM;
ALTER TABLE feedback_person ENGINE = MyISAM;
ALTER TABLE idp ENGINE = MyISAM;
ALTER TABLE incident ENGINE = MyISAM;
ALTER TABLE incident_category ENGINE = MyISAM;
ALTER TABLE incident_person ENGINE = MyISAM;
ALTER TABLE level ENGINE = MyISAM;
ALTER TABLE location ENGINE = MyISAM;
ALTER TABLE media ENGINE = MyISAM;
ALTER TABLE organization ENGINE = MyISAM;
ALTER TABLE organization_incident ENGINE = MyISAM;
ALTER TABLE pending_users ENGINE = MyISAM;
ALTER TABLE reporter ENGINE = MyISAM;
ALTER TABLE service ENGINE = MyISAM;
ALTER TABLE sessions ENGINE = MyISAM;
ALTER TABLE sharing ENGINE = MyISAM;
ALTER TABLE sharing_log ENGINE = MyISAM;
ALTER TABLE verified ENGINE = MyISAM;
 
ALTER TABLE alert_sent CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE category_lang CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE cluster CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE comment CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feed CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feedback CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feedback_person CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feed_item CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE incident_lang CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE laconica CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE level CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE message CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE rating CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE reporter CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE scheduler CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE scheduler_log CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE service CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE twitter CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
 
ALTER TABLE `settings` ADD `db_version` VARCHAR( 20 ) NOT NULL ,
ADD `ushahidi_version` VARCHAR( 20 ) NOT NULL ;
 
UPDATE `settings` SET `db_version` = '11',
`ushahidi_version` = '0.9' WHERE `id` =1 LIMIT 1 ;
