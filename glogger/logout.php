<?php
/*
Glogger: Goolge Spread Sheet Logger.
*/
require_once('config.php');
require_once('functions.php');

check_session();

$_SESSION = array();

if (isset($_COOKIE[session_name()])){
	setcookie(session_name(), '', time()-86400, '/mqueue/');
}
session_destroy();
header('Location: '. SITE_URL);