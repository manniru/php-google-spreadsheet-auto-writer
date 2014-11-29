<?php
/*
Glogger: Goolge Spread Sheet Logger.
FUnction: Change spreadsheet file name.

 */
require_once('config.php');
require_once('functions.php');

check_session();
$next_file_name = htag2c($_REQUEST['next_file_name']);

if (! empty($next_file_name) ){
	$dbh = connectDB();
	$sql = "update glusers set active_file_name = :next_file_name where google_user_id = :user_id";
	$stmt = $dbh -> prepare($sql);
	$stmt -> bindParam(":user_id", $user_id);
	$stmt -> bindParam(":next_file_name", $next_file_name);
	$stmt -> execute();
}
header('Location: ' . SITE_URL .'admi.php');