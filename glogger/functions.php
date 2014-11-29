<?php 
function connectDB(){
	$dbh = new PDO(DSN, DB_USER, DB_PASSWORD);
	$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	try {
		return $dbh;
	} catch (PDOExcption $e){
		echo 'DB Connection failed.' . $e->getMessage();
		exit;
	} 
}

// HTML tag chaged into charactors.
function htag2c($s){
	return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}
// Short message I18n
function genmsg($msgs){
	return $msgs[LANG];
}
// Renderer with I18n
function render($template, $params=array() ){
	extract($params);
	ob_start();
	include VIEW_DIR . LANG . "/$template";
	$html = ob_get_contents();
	ob_end_clean();
	return $html;	
}
// Session
function check_session(){
	global $user_id, $email, $name;
	session_set_cookie_params(0, '/glogger/');
	session_start();
	if (empty($_SESSION[SID])){
		header('Location: ' . SITE_URL . 'login.php');	 
		exit;
	} else {
		// follows are must be kept.
		$user_id = $_SESSION[SID]['user_id'];
		$email = $_SESSION[SID]['email'];
		$name = $_SESSION[SID]['name'];
	}
} 


