<?php
    $doc_category="                    
					CREATE TABLE IF NOT EXISTS `doc_category` (
					  `category_id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `category_name` varchar(250) NOT NULL,
					  `disabled` smallint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`category_id`),
					  UNIQUE KEY `category_name` (`category_name`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
                 ";
	$doc_ext=    "                  
					CREATE TABLE IF NOT EXISTS `doc_extensions` (
					  `ext_id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `ext_name` varchar(100) NOT NULL,
					  `disabled` smallint(6) NOT NULL,
					  PRIMARY KEY (`ext_id`),
					  UNIQUE KEY `ext_name` (`ext_name`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

                 ";
	$doc_manager="                    
					CREATE TABLE IF NOT EXISTS `doc_manager` (
				  `doc_id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `category_id` bigint(20) DEFAULT NULL,
				  `title` varchar(300) DEFAULT NULL,
				  `description` text,
				  `doc_url` text NOT NULL,
				  `doc_path` text NOT NULL,
				  `doc_ext` varchar(50) DEFAULT NULL,
				  `doc_type` varchar(200) DEFAULT NULL,
				  `post_date` date NOT NULL,
				  `post_time` varchar(60) NOT NULL,
				  `disabled` smallint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`doc_id`),
				  KEY `category_id` (`category_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;
					
					
                 ";
				 
	 $extra="ALTER TABLE `doc_manager`
  ADD CONSTRAINT `doc_manager_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `doc_category` (`category_id`);";
     global $wpdb;
     $wpdb->query($doc_category);
	 $wpdb->query($doc_ext);
	 $wpdb->query($doc_manager);
	 $wpdb->query($extra);

?>
