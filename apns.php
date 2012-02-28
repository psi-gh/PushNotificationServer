#!/usr/bin/php
<?PHP
#################################################################################
## Developed by Manifest Interactive, LLC                                      ##
## http://www.manifestinteractive.com                                          ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##
##                                                                             ##
## THIS SOFTWARE IS PROVIDED BY MANIFEST INTERACTIVE 'AS IS' AND ANY           ##
## EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE         ##
## IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR          ##
## PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL MANIFEST INTERACTIVE BE          ##
## LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR         ##
## CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF        ##
## SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR             ##
## BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,       ##
## WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE        ##
## OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,           ##
## EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                          ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##
## Author of file: Peter Schmalfeldt                                           ##
#################################################################################

/**
 * @category Apple Push Notification Service using PHP & MySQL
 * @package EasyAPNs
 * @author Peter Schmalfeldt <manifestinteractive@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link http://code.google.com/p/easyapns/
 */

/**
 * Begin Document
 */

// AUTOLOAD CLASS OBJECTS... YOU CAN USE INCLUDES IF YOU PREFER
if(!function_exists("__autoload")){ 
	function __autoload($class_name){
		require_once('classes/class_'.$class_name.'.php');
	}
}

// CREATE DATABASE OBJECT ( MAKE SURE TO CHANGE LOGIN INFO )
$db = new DbConnect('localhost', 'apnsuser', 'apnspassword', 'apnsdb');
$db->show_errors();

// FETCH $_GET OR CRON ARGUMENTS TO AUTOMATE TASKS
$args = (!empty($_GET)) ? $_GET:array('task'=>$argv[1]);

// CREATE APNS OBJECT, WITH DATABASE OBJECT AND ARGUMENTS
$apns = new APNS($db, $args);
//---------------------------------------------------------------------------------------
$url = "https://www.google.com/accounts/ClientLogin";
$accountType = 'GOOGLE'; //Doesn't change for this
$email = 'testneirontalk@gmail.com'; //Enter your Google Account email
$password = '55555qwerty';  //Enter your Google Account password
$registrationId = $args['devicetoken'];
print($registrationId."<br>");
$source = 'Talkr-Neiron-1'; //Enter a name for this source of the login
$service = 'ac2dm'; //Select which service you want to log into

//Once that is all done it’s time to use some cURL to send our request and retrieve the auth token:
$ch = curl_init();
$URL = $url."?accountType=".$accountType."&Email=".$email."&Passwd=".$password."&source=".$source."&service=".$service;


// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

//Divide the response into an array as to find the auth token
$line = explode("\n", $response);


// close cURL resource, and free up system resources
curl_close($ch);
unset($ch);
unset($response);

$auth_token = str_replace("Auth=", "", $line[2]); //auth token from Google Account Sign In

//$auth_token="DQAAANAAAACg1nps2w0RoZT5tsKA875kmxAZ4THHqERMx9tG7nuhmCiC5UU8WOdj1vB3gpUA1mRPDHTbVtoa4ESE2vHiOqMeomtUYwaQNQmbmIDY3KraHTTHrjte72nIcRSa3ptjeCpTw-ZYBJ9EvSZf64eI611xopKy6qoCi4WX7u8aMx8CJkhV22vMlJYWBpYsd7VhW95xOcJUo8-Kc1xJpmLk2MyyFAdXG-XNZB7rBGjweZTP3LNh9lKB4A4cTygJTCtnUIXr9ULmLqlFhd2LpkOzgMCk";

$messageUrl = "https://android.apis.google.com/c2dm/send";
$collapseKey = "storedmessages";
//sleep(20);
$mes = date("H:i:s");
print($mes."<br>");
$data = array('data.message'=>$mes); //The content of the message
print($data ."<br>");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $messageUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


$header = array("Authorization: GoogleLogin auth=".$auth_token); //Set the header with the Google Auth Token
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

$postFields = array("registration_id" => $registrationId, "collapse_key" => $collapseKey, "delay_while_idle"=>"");
$postData = array_merge($postFields, $data);
print_r($postData);
print("<br>");
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

$response = curl_exec($ch);
//Print response from C2DM service//
print_r("<h1>".$response."</h1>");

// close cURL resource, and free up system resources
curl_close($ch);
//------------------------------------------------------------------------
?>
