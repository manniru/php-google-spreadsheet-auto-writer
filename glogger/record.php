<?php
/*
Glogger: Goolge Spread Sheet Logger.
Function: this program will be called like this format:
 	http://device.mind-craft.net/glogger/record.php?
 	email=mail&data=xxxxx&column=record&sheet=sheet2
 Get format only allowed. 
 Default value of sheet = シート1/Sheet1
 Default value of column = data
 *** time column is required.
 */
require_once('config.php') ;
require_once('functions.php') ;

// This program should not have any 'SESSION'. just only one
// FIrst of all, only allow GET request
if($_SERVER["REQUEST_METHOD"] == "POST"){
	header('HTTP/1.1 403 Forbidden.') ; // forbidden
	echo 'Not allowed access method.' ;
	exit;
}
// sanitizing.
if (empty($_GET['column'])){
	$column = "data"; // default value.
} else {
	$column = htag2c( $_GET['column'] );
}
if (empty( $_GET['sheet'] )){
	$sheet = DEFAULT_SHEET_NAME ; // default value
} else { 
	$sheet = htag2c($_GET['sheet'] );
}
// Email check
if (empty($_GET['email'])){
	header('HTTP/1.1 400 Bad Request'); // Bad Request
	echo 'email parameter not found.';
	exit;
}	
$email = htag2c( $_GET['email'] );

if (! filter_var( $email, FILTER_VALIDATE_EMAIL)){
	header('HTTP/1.1 400 Bad Request'); // Bad Request
	echo 'mail address ' . $email . ' is not a valid mail format.';
	exit;
}
// data check
if ( empty( $_GET['data'] )){
	header('HTTP/1.1 400 Bad Request.'); // Bad Request
	echo 'data parameter is not found.';
	exit;
}
$data = htag2c( $_GET['data'] );
if (! filter_var($data, FILTER_VALIDATE_FLOAT)){
	header('HTTP/1.1 400 Bad Request.'); // Bad Request
	echo 'data is not numeric.';
	exit;
}
// Personal data read from DB.
$dbh = connectDB();
$sql = "select * from glusers where google_email = :email" ;
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":email", $email);
$stmt->execute();
$user = $stmt->fetch();
if (empty($user) ){
// empty no user. bye
	header('HTTP/1.1 404 Not Found'); // not found.
	echo 'Email ' . $email . ' is not a user.' ;
	exit;
} else {
// associative array -> variable.
	extract($user, EXTR_OVERWRITE); 
	$access_token = $google_access_token;
	$refresh_token = $google_refresh_token; 
	$expires_in = $google_expires_in;
	$file_name = $active_file_name;
	// $used = $used;
}
$stmt->closeCursor(); // Close SQL process.

// Check data frequency 10 second rule
// but lolipop database system and web server timestamp is diffent.
if ( (time() - strtotime($used)) < 10 ){
	 		header('HTTP/1.1 400 Bad Request.'); // Too Early
	 		echo 'data process frequency is over 10 seconds.'.
		exit;
}
// Validate Access Token.
if ( (time() - 10 ) > ( $used + $expires_in ) ){
	// refresh_access_token
	$baseURL = "https://accounts.google.com/o/oauth2/token" ;
	$params = array(
		'refresh_token' => $refresh_token,
		'client_id' => CLIENT_ID,
		'client_secret' => CLIENT_SECRET,
		'grant_type' => 'refresh_token'
	);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $baseURL) ;
	curl_setopt($curl, CURLOPT_POST,1) ;
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params)) ;
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
	
	$rs = curl_exec($curl) ;
	curl_close($curl) ;

	$response = json_decode($rs) ;
	if ( ! empty( $response->error) ){
		header('HTTP/1.1 500 Internal Error.'); // not found.
		echo "Hoops, new access_token could not be gotten." ;
		exit;
	} 
	$access_token = $response -> access_token ;
}
// update used  date
$sql = "update glusers set google_access_token = :access_token, used = now() where  google_email = :email" ;
$stmt = $dbh->prepare($sql) ;
$stmt -> bindParam(":access_token", $access_token) ;
$stmt -> bindParam(":email", $email) ;
$stmt -> execute() ;

// Google Spreadsheet Client Bootstrapping
require('./vendor/autoload.php') ;
use Google\Spreadsheet\DefaultServiceRequest ;
use Google\Spreadsheet\ServiceRequestFactory ;

$serviceRequest = new DefaultServiceRequest($access_token) ;
ServiceRequestFactory::setInstance($serviceRequest) ;

// Adding a row
$spreadsheetService = new Google\Spreadsheet\SpreadsheetService() ;
$spreadsheetFeed = $spreadsheetService->getSpreadsheets() ;
$spreadsheet = $spreadsheetFeed->getByTitle($file_name) ;
if ( ! is_object($spreadsheet) ){
	header('HTTP/1.1 404 Not Found') ; // not found.
	echo  'FIle '. $file_name . " is not found." ;
	exit;
}
$worksheetFeed = $spreadsheet->getWorksheets() ;
$worksheet = $worksheetFeed->getByTitle($sheet) ;
if ( ! is_object($worksheet) ){
	header('HTTP/1.1 404 Not Found'); // not found.
	echo 'Sheet ' . $sheet . " is not found." ;
	exit;
}

$listFeed = $worksheet->getListFeed() ;

$row = array('time' => date( 'Y/m/d H:i:s'), $column => $data ) ;
$listFeed->insert($row) ;

header("HTTP/1.1 200 OK") ;

//echo "<pre>";
//foreach ($listFeed->getEntries() as $entry){
//  $values = $entry->getValues();
//  print_r($values);
//}
//$url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $access_token;
//$userinfo = json_decode(@file_get_contents($url));
//if (empty($userinfo)){

  