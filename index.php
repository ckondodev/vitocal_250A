<?php

include('db.php');

$clientid='<GUID>'; 
$code_verif='...................'; 


$url = 'https://iam.viessmann.com/idp/v2/token';
$data = array(
'grant_type'=>'authorization_code',
'client_id'=>$clientid,
'redirect_uri' => 'http://<URI der Webseite>/<Path>/',
'code_verifier' => $code_verif,
'code'=>$_GET['code']);


$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$json = json_decode($result, true);
$token = $json['access_token'];
$refresh = $json['refresh_token'];
storeTokens($token, $refresh);
?>