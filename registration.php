<?php
# https://secure.yourwebsite.com/apns.php?task=register&appname=My%20App&appversion=1.0.1&deviceuid=e018c2e46efe185d6b1107aa942085a59bb865d9&devicetoken=43df9e97b09ef464a6cf7561f9f339cb1b6ba38d8dc946edd79f1596ac1b0f66&devicename=My%20Awesome%20iPhone&devicemodel=iPhone&deviceversion=3.1.2&pushbadge=enabled&pushalert=disabled&pushsound=enabled

$params = $_GET;
if (empty($params))
{
print("No arguments.");
die();
}
$devicetoken = $params['devicetoken'];
$jid = $params['jid'];
$devicemodel = $params['devicemodel'];
#$devicetoken = $params['appname'];
#$devicename = $params['appname'];
#$devicemodel = $params['appname'];
#$deviceversion = $params['appname'];
#$pushbadge = $params['appname']; 
#$pushalert = $params['appname']; 
#$pushsound = $params['appname']; 
#$clientid = $params['appname'];


#if(strlen($jid)==0) $this->_triggerError('Application jid must not be blank.', E_USER_ERROR);
#if(strlen($devicetoken)!=64) $this->_triggerError('Device Token must be 64 characters in length.', E_USER_ERROR);
#if(strlen($devicemodel)==0) $this->_triggerError('Device Model must not be blank.', E_USER_ERROR);

// AUTOLOAD CLASS OBJECTS... YOU CAN USE INCLUDES IF YOU PREFER
if(!function_exists("__autoload")){ 
	function __autoload($class_name){
		require_once('classes/class_'.$class_name.'.php');
	}
}

$config = parse_ini_file("config", true);
print_r($config);
$address = $config['DATABASE']['address'];
$login = $config['DATABASE']['login'];
$pas = $config['DATABASE']['password'];
$dbname = $config['DATABASE']['db_name'];

$db = new DbConnect($address, $login, $pas, $dbname);
$db->show_errors();

$jid = $db->prepare($jid);
$devicetoken = $db->prepare($devicetoken);
$devicemodel = $db->prepare($devicemodel);
// store device for push notifications
$db->query("SET NAMES 'utf8';"); // force utf8 encoding if not your default
$sql = "INSERT INTO `apns_devices`
		VALUES (
			NULL,
			'{$jid}',
			'',
			'',
			'',
			'',
			'{$devicetoken}',
			'',
			'{$devicemodel}',
			'',
			'',
			'',
			'',
			'production',
			'active',
			NOW(),
			NOW()
		)
		ON DUPLICATE KEY UPDATE
		`devicetoken`='{$devicetoken}',
		`devicemodel`='{$devicemodel}',
		`status`='active',
		`modified`=NOW();";
$db->query($sql);
	
print("Done.");

?>
