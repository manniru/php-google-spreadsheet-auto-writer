# Introduction

This is simple PHP server program.
Many pepole want to send data from small computer, such as Arduino to your Google SpreadSheet,
A few years ago, it was simple. You can just add Google User ID and password.
But now, Google allows only OAuth2 authentification.

If you have a small space on your own/rental servers with PHP and MySQL environment, you can become into 'gateway to Google Spread sheet'.

#Installation
Your work as Host are;

1. Register your Project in Google API console. Each step is described here. (https://developers.google.com/console/help/new/)
   If you know more detail, read  (https://developers.google.com/accounts/docs/OAuth2) 

2. In OAuth area, you may have Client ID, Client Secret, Redirect URL(You must specify).And you can specify Agrremtn screen contents.

3. Create database table. Database location, database name is up to you. Table definition is as follows;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

4. Download this package. (I don't support composer.)

5. Adjust 'config.php' constant values.
	LANG - 'en' or 'jp'
	DSN - Database environment like, 'mysql:dbname=xxxxx;host=localhost'
	DB_USER - database user id.
	DB_PASSWORD - database user password.
	CLIENT-ID - you got it at step2.
	CLIENT_SECRET - you got it at step2.
	CALL_BACK - you got it at steop2.
	SITE_URL - URL where you put these PHP programs.

6. Upload all PHP files on your server space.

7. try to access http://yourURL/gloggger/index.php

8. You can specify Google Spreadsheet name. Default is 'glogger_test'

9. Make sure $_GET['sheet'] default value in record.php.

# Preparation
You must make a spreadsheet. It must has two column name on the line 1.
One is 'time' (case sensitive), the other one is basically 'data'.
And FREEZE FIRST ROW as column name.
To freeze;
1. Goto the View menu.
2. point Freeze Rows.
3. Select one of the options. 'freeze 1 row'

# Usage
Once you register your Google account on the table by server setup programs, you can use record.php

Basic format is; http://[yourURL]/glogger/record.php?email=[your registered mail address]&data=[nnnn]

Parameter email and data are required.

options:
column= can change column name from data to other name as you like.
sheet= can change sheet name. (Please give me good idea to change this default value. English and Japanese are diffrent.)
