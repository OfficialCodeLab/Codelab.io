<?php
/**
 * Hotel reservation submit
 */

define('_EMAIL_TO', 'wowthemesnet@gmail.com'); // your email address where reservation details will be received
define('_EMAIL_SUBJECT', 'Cayman Reservation Form'); // email message subject
define('_EMAIL_FROM', $_POST["email"]);

$fields = array(
	array('name' => 'date-from', 'title' => 'From', 'valid' => array('require'), 'err_message' => ''),
	array('name' => 'date-to', 'title' => 'To', 'valid' => array('require')),
	array('name' => 'room-type', 'title' => 'Room', 'valid' => array('require')),
	array('name' => 'room-requirements', 'title' => 'Room requirements'),
	array('name' => 'adults', 'title' => 'Adults', 'valid' => array('require')),
	array('name' => 'children', 'title' => 'Children', 'valid' => array('require')),
	array('name' => 'name', 'title' => 'Name', 'valid' => array('require')),
	array('name' => 'email', 'title' => 'Email', 'valid' => array('require')),
	array('name' => 'phone', 'title' => 'Phone', 'valid' => array('require')),
	array('name' => 'special-requirements', 'title' => 'Special requirements')
);

$error_fields = array();
$email_content = array();
foreach ($fields AS $field){
	$value = isset($_POST[$field['name']])?$_POST[$field['name']]:'';
	$title = empty($field['title'])?$field['name']:$field['title'];
	$email_content[] = $title.': '.nl2br(stripslashes($value));
	$is_valid = true;
	$err_message = '';
	if (!empty($field['valid'])){
		foreach ($field['valid'] AS $valid) {
			switch ($valid) {
				case 'require':
					$is_valid = $is_valid && strlen($value) > 0;
					$err_message = 'Field required';
					break;
				case 'email':
					$is_valid = $is_valid && preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $value);
					$err_message = 'Email required';
					break;
				default:				
					break;
			}
		}
	}
	if (!$is_valid){
		if (!empty($field['err_message'])){
			$err_message = $field['err_message'];
		}
		$error_fields[] = array('name' => $field['name'], 'message' => $err_message);
	}
}

if (empty($error_fields)){
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers = "From: "._EMAIL_FROM."\r\n"; 
	$headers .= "Reply-To: "._EMAIL_FROM."\r\n"; 	
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	// Send email
	mail (_EMAIL_TO, _EMAIL_SUBJECT, implode('<hr>', $email_content), $headers);
	echo (json_encode(array('code' => 'success')));
}else{
	echo json_encode(array('code' => 'failed', 'fields' => $error_fields));
}