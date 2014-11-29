<?php
/*
Glogger: Goolge Spread Sheet Logger.
*/
require_once('config.php');
require_once('functions.php');

check_session();	

$parms = array( 
	'myname' =>$_SESSION[SID]['name'],	
);
echo render('index.html', $parms); 
?>
