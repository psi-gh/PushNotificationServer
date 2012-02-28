<?php
#$url = "https://www.google.com/accounts/ClientLogin";
#$accountType = 'GOOGLE'; //Doesn't change for this
#$email = 'belozerow@gmail.com'; //Enter your Google Account email
#$password = '5555555555qwerty';  //Enter your Google Account password
$registrationId = "APA91bEflJbfS6_XmOP5QHlugSjStfBwn6-YsyqOuPAaSXa2hDsKwIY5tV0pZqKhjJ2Ckd2DagZ_kyRpiMgJbyfliSeCGcgtxFyN59o15GmHFJcQXHL_xIevUf1WENd7J5UzOcGxfpNx4w8iP_dLEsOeukixjkQ6IA";
#$source = 'Talkr-Neiron-1'; //Enter a name for this source of the login
#$service = 'ac2dm'; //Select which service you want to log into

#//Once that is all done it’s time to use some cURL to send our request and retrieve the auth token:
#$ch = curl_init();
#$URL = $url."?accountType=".$accountType."&Email=".$email."&Passwd=".$password."&source=".$source."&service=".$service;


#// set URL and other appropriate options
#curl_setopt($ch, CURLOPT_URL, $URL);
#curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
#$response = curl_exec($ch);

#//Divide the response into an array as to find the auth token
#$line = explode("\n", $response);


#// close cURL resource, and free up system resources
#curl_close($ch);
#unset($ch);
#unset($response);

#$auth_token = str_replace("Auth=", "", $line[2]); //auth token from Google Account Sign In
$auth_token = "DQAAAL4AAAA3fFx4pKdtgtxMUMPHrKvNFpYPrQzm667u3A1jWUNHrLl2WtBDdqZSroe1SZsBVlPn1eVnCeVsM9Q7DsfQ2BxqgrgEw4TsiXNEQFnleBhEMQHJQwUoqERqW4X5OZFzUbIW94BT3PNITF8qcj3fjXaCyOoVG1YF9OEv6pTlp4ppxZvzkMtkurlIjb7bY_yfHMvVRYN8k3IhlDCSzDit3IhYbELK313rcHAIxkINW2ij6siENIrNnjduIy65QaSOAaY";

$messageUrl = "https://android.apis.google.com/c2dm/send";
$collapseKey = "storedmessages";
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
?>
