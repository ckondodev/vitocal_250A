<?php
    include('db.php');

    $refreshToken = getRefreshTokenfromDB();
    
    // get new token
    $clientid='<GUID>'; 
    
    $url = 'https://iam.viessmann.com/idp/v2/token';
    $data = array(
    'grant_type'=>'refresh_token',
    'client_id'=>$clientid,
    'refresh_token'=>$refreshToken,
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

    echo ("<H2>Token:</H2>");
    echo ($refreshToken);
    echo ("<BR> --> <BR>");
    echo ($token);
    echo ("<BR> / <BR>");
    echo ($refresh);

    if ($refresh == $refreshToken) {
        storeTokens($token, $refresh);
    } else {
        $urlNewRequest="https://iam.viessmann.com/idp/v2/authorize?client_id=<GUID>&redirect_uri=http://<URI>/<PATH>/&response_type=code&code_challenge=<ChallangeCode>&scope=IoT%20User%20offline_access";
        echo ("<H2> Authentification error: </H2>");
        echo ("<BR>");
        echo $urlNewRequest;
        exit;
   }

    // get events

    echo "<H2>Events:</H2>";
    $url2='https://api.viessmann.com/iot/v1/events-history/events?limit=1000&gatewaySerial=<Serialnumber of local gateway>';

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url2,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array("Authorization: Bearer $token"),
    ));
    
    $response = curl_exec($curl);    
    $result2 = json_decode($response, true);

    $data2 = $result2['data'];
    $i=0;
    foreach ($data2 as $event) {
            $type = $event['eventType'];
            $code = $event['body']['errorCode'];
            $status = "active";
            if ($event['body']['active'] == 0) {
                $status = "deactivated";
            }
            $device = $event['body']['equipmentType'];
            $time = $event['eventTimestamp'];
            $jsonEvent = json_encode($event);
            $i=$i+1;
            echo("<BR><b>");     
            echo("#$i : Code = $code, $status, Time = $time, Device = $device ($type)");
            echo("</b><BR>");
            echo($jsonEvent);
            storeHeatPumpEvent($time, $code, $jsonEvent);
            echo("<BR>&nbsp;<BR>");
        }

    curl_close($curl);

    echo("<BR>&nbsp;<BR>");
    var_dump($result2);
?>