<?php
/* Glogger: Goolge Spread Sheet Logger.
* Google oAuth call back 
*/
require_once('config.php');
require_once('functions.php');
session_start();
if ($_SESSION['state'] != $_REQUEST['state']) {
	$msgs = array( 
		'jp' => "エラー. セッション順序が違います",
		'en' => "Error. Invalid page call sequence."
	);
    echo genmsg($msgs);
    exit;
}
if (! empty($_REQUEST['error']) ){
	$msgs = array(
		'jp' => "エラー. 認証されませんでした。バイバイ",
		'en' => "Error. not Authorized by user."
	);
	echo genmsg($msgs);
	exit;
}

$code = $_REQUEST['code'];

//Get Access Token
$baseURL = 'https://accounts.google.com/o/oauth2/token';
$params = array(
	'code'            => $code,
	'client_id'       => CLIENT_ID,
	'client_secret' => CLIENT_SECRET,
	'redirect_uri'   => CALL_BACK,
	'grant_type'    => 'authorization_code'
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $baseURL);
curl_setopt($curl, CURLOPT_POST,1);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
$rs = curl_exec($curl);
curl_close($curl);
$response = json_decode($rs);
if (! empty($response->error)){
	$msgs = array(
		'jp' => "エラー。<a href='getcode.php'>最初からお願いします。</a><br/>",
		'en' => "Error. <a href='getcode.php'>Please restart.</a><br/>"
		);
	echo genmsg($msgs);	
	echo $response->error;
	exit;
}
// echo "<pre>"; print_r($response);exit;
$access_token = $response -> access_token;
$refresh_token = $response -> refresh_token;
$expires_in = $response -> expires_in;

// get user info
$userinfo = json_decode(
	file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?' .
	'access_token=' . $access_token)
);
$user_id = $userinfo -> id;
$name = $userinfo -> name;
$email = $userinfo -> email;

//  info into DB.
$dbh = connectDB();
// check already defined.
$sql = "select * from glusers where google_user_id = :user_id";
$stmt = $dbh -> prepare($sql);
$stmt -> bindParam(':user_id', $user_id);
$stmt -> execute();
$user = $stmt -> fetch(PDO::FETCH_ASSOC);
if ( empty($user) ){
  // insert data.
	$sql = "INSERT INTO glusers " .
	"(google_user_id, google_email, google_name, google_access_token, google_refresh_token, google_expires_in, active_file_name, created, used)  ".
	"VALUES( :user_id, :email, :name, :access_token, :refresh_token, :expires_in, :file_name, now(), now() ) ";
	$stmt = $dbh -> prepare($sql);
	$stmt -> bindParam(':user_id', $user_id);
	$stmt -> bindParam(':email', $email);
	$stmt -> bindParam(':name', $name);
	$stmt -> bindParam(':access_token', $access_token);
	$stmt -> bindParam(':refresh_token', $refresh_token);
	$stmt -> bindParam(':expires_in', $expires_in);
	$stmt -> bindValue(':file_name', DEFAULT_FILE_NAME );
	$stmt -> execute();
}
// set session data
$_SESSION[ SID ] = array(
	'user_id' => $user_id,
	'email' => $email,
	'name' => $name
);

header('Location: ' . SITE_URL . 'admi.php');