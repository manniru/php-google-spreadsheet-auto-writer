<?php
/*
Glogger: Goolge Spread Sheet Logger.
Funticon: Change spreadsheet file name.

 */
require_once('config.php');
require_once('functions.php');

check_session();

$dbh = connectDB();
$sql = "select active_file_name from glusers where google_user_id = :id ";
$stmt = $dbh->prepare($sql);
$stmt -> bindParam(":id", $user_id);
$stmt -> execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($user)){
	echo "Error. Invalid User.";
	exit;
} 

$data = array();
$data['current_file_name'] = $user['active_file_name'];
echo render('admi.html', $data);


?>
