<?php

#http://webgranuls.dyndns.org:82/apns_talk/pushmessage.php?from=hui@prostoy&to=test3@jid.ru&body=qweqwqwdqw

function write_ini_file($assoc_arr, $path, $has_sections=FALSE) { 
    $content = ""; 
    if ($has_sections) { 
        foreach ($assoc_arr as $key=>$elem) { 
            $content .= "[".$key."]\n"; 
            foreach ($elem as $key2=>$elem2) { 
                if(is_array($elem2)) 
                { 
                    for($i=0;$i<count($elem2);$i++) 
                    { 
                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = \"".$elem2."\"\n"; 
            } 
        } 
    } 
    else { 
        foreach ($assoc_arr as $key=>$elem) { 
            if(is_array($elem)) 
            { 
                for($i=0;$i<count($elem);$i++) 
                { 
                    $content .= $key2."[] = \"".$elem[$i]."\"\n"; 
                } 
            } 
            else if($elem=="") $content .= $key2." = \n"; 
            else $content .= $key2." = \"".$elem."\"\n"; 
        } 
    } 

    if (!$handle = fopen($path, 'w')) { 
        return false; 
    } 
    if (!fwrite($handle, $content)) { 
        return false; 
    } 
    fclose($handle); 
    return true; 
}

function registration_android($email, $password)
{
        $url = "https://www.google.com/accounts/ClientLogin";
        $accountType = 'GOOGLE'; //Doesn't change for this
#        $email = 'testneirontalk@gmail.com'; //Enter your Google Account email
#        $password = '55555qwerty';  //Enter your Google Account password
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
        return $auth_token;
}

function model($to)
{
    // CREATE DATABASE OBJECT ( MAKE SURE TO CHANGE LOGIN INFO IN CLASS FILE )
    $db = new DbConnect('localhost', 'pavel', '5555', 'apns_talk');
    $db->show_errors();
    $sql = "SELECT * FROM `apns_devices` WHERE `jid`='{$to}'";
    print($sql.'<br>');
    $result = $db->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    print('Number of rows '.$result->num_rows.'<br>');
    return $row;
}


$config = parse_ini_file("config", true);
print_r($config);
$params = $_POST;

if (!empty($params))
{
    $from = $params['from'];
    $to = $params['to'];
    $body = $params['body'];
}
else
{
    echo "No arguments.";
    break;
}

// AUTOLOAD CLASS OBJECTS... YOU CAN USE INCLUDES IF YOU PREFER
if(!function_exists("__autoload"))
{ 
	function __autoload($class_name)
	{
		require_once('classes/class_'.$class_name.'.php');
	}
}

$r = model($to);
print_r($r);
print("<br>");

//devicemodel iOS or Android
if ($r['devicemodel'] == "iOS")
{
    $sandboxCertificate = '/usr/local/apns/apns-dev.pem';
	$sandboxSsl = 'ssl://gateway.sandbox.push.apple.com:2195';
    #$sandboxFeedback = 'ssl://feedback.sandbox.push.apple.com:2196';
#    $message = '{"aps":{"clientid":"null","alert":"'.$body.'"},"acme2":["bang","whiz"]}';
    $message = '{"aps":{"clientid":"null","alert":"'.$body.'"},"jid":"'.$from.'"}';
    $token = $r['devicetoken'];
    print($token);
    print_r($message);
    print("<br>");
    $ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', $sandboxCertificate);
	$fp = stream_socket_client($sandboxSsl, $error, $errorString, 100, (STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT), $ctx);

	if(!$fp){
        print("error in stream_socket_client");
	}
	else {
		$msg = chr(0).pack("n",32).pack('H*',$token).pack("n",strlen($message)).$message;
		$fwrite = fwrite($fp, $msg);
		print($msg."<br>");
		if(!$fwrite) {
            print("error in fwrite");
		}
		else {
			print("success");
		}
	}
	fclose($fp);
}
else
{
   #$auth_token="DQAAALwAAADbeCNS5MvKxrq--zAWUuDldcmyXKyIUZYA7xuqvM3pqXtHD3bXn03V_HWaHgFTeK0DBHDQdyD9iJlSuR8IWirgMUrVyu7e_Fphcdp2KHvZeDIi95Nsk1_kV41L-lsyuJ0SR2GcPs3OK62MgoetIDPNYwXxaw87PKjlcRUPtlBoQ5dx8EdG0hvpDLwdXSZej635wNyh6ePEim4ckLkeJ80fDrFl3oUpZwreXx_UDtVpAAijroMpRxB7Trp9dNsjrps";
    $auth_token =  $config['MAIN']['auth_token'];
    $messageUrl = "https://android.apis.google.com/c2dm/send";
    $collapseKey = "storedmessages";
    $mes = date("H:i:s");
    print($mes."<br>");
    $data = array('data.message'=>$body); //The content of the message
    print_r($data);
    print("<br>");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $messageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $header = array("Authorization: GoogleLogin auth=".$auth_token); //Set the header with the Google Auth Token
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $postFields = array("registration_id" => $r['devicetoken'], "collapse_key" => $collapseKey, "delay_while_idle"=>"");
    $postData = array_merge($postFields, $data);
    print_r($postData);
    print("<br>");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    //Print response from C2DM service//
    print_r("<h1>".$response."</h1>");
    
    if (strpos($response, "Error")!=false)
    {
        print('receiving new token<br>');
        $config['MAIN']['auth_token'] = registration_android($config['MAIN']['email'], $config['MAIN']['password']);
        write_ini_file($config, './config', true);
    }

    // close cURL resource, and free up system resources
    curl_close($ch);
}
?>
