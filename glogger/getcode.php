<?php
/*
Glogger: Goolge Spread Sheet Logger.
Function: Google ID get. When here comes, there is no session.
 */
require_once('config.php');
session_start();

$baseURL = 'https://accounts.google.com/o/oauth2/auth?';
$scope = array(
	'https://www.googleapis.com/auth/userinfo.profile',
	'https://www.googleapis.com/auth/userinfo.email',
	'https://spreadsheets.google.com/feeds'
) ;
$_SESSION['state'] = sha1(uniqid(mt_rand(), true));
$params = array(
		'client_id' => CLIENT_ID,
		'redirect_uri' => CALL_BACK,
		'state' => $_SESSION['state'],
		'approval_prompt' => 'force',  // usually, auto
		'scope' =>  implode(' ', $scope) ,
		'response_type' => 'code',
		'access_type' => 'offline'
	);
$authURL = $baseURL . http_build_query($params);
//Redirect
header('Location: ' . $authURL);