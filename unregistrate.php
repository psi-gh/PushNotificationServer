<?php
$params = $_GET;
if (empty($params))
{
print("No arguments.");
die();
}
#$devicetoken = $params['devicetoken'];
$jid = $params['jid'];

// AUTOLOAD CLASS OBJECTS... YOU CAN USE INCLUDES IF YOU PREFER
if(!function_exists("__autoload")){ 
	function __autoload($class_name){
		require_once('classes/class_'.$class_name.'.php');
	}
}

$db = new DbConnect('localhost', 'pavel', '5555', 'apns_talk');
$db->show_errors();

$sql = "DELETE FROM `apns_devices` WHERE `jid`='{$jid}'";
print($sql."<br>");
$db->query($sql);
print("Done.");

?>
