<?php
/* Glogger: Goolge Spread Sheet Logger.
 
Required Table Format:
	CREATE TABLE IF NOT EXISTS `glusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_user_id` varchar(50) DEFAULT NULL,
  `google_email` varchar(255) DEFAULT NULL,
  `google_name` varchar(255) DEFAULT NULL,
  `google_access_token` varchar(255) DEFAULT NULL,
  `google_refresh_token` varchar(255) DEFAULT NULL,
  `google_expires_in`  smallint DEFAULT NULL,
  `active_file_name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `used` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `google_user_id` (`google_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;
*/
// Lang
define('LANG', 'en');
// View directory
define('VIEW_DIR', './view/');
// DB
define('DSN', 'mysql:dbname=xxxx;host=localhost');
define('DB_USER', 'dbusername');
define('DB_PASSWORD', 'dbuser password') ;
// Current target file. 
define('DEFAULT_FILE_NAME', 'glogger_test');
// Google API Console
define('CLIENT_ID','get from Goolge');
define('CLIENT_SECRET','get from Google');
define('SID','glogger');
define('CALL_BACK', 'specify your site URL plus redirect.php ');
define('SITE_URL', 'specify your site URL');

// Google Sheet Name
define('DEFAULT_SHEET_NAME', "sheet1"); // Japanese シート1


