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
        $urlNewRequest="https://iam.viessmann.com/idp/v2/authorize?client_id=<clientID>&redirect_uri=http://<URI>/&response_type=code&code_challenge=<ChallangeCode>&scope=IoT%20User%20offline_access";
        echo ("<H2> Authentification error: </H2>");
        echo ("<BR>");
        echo $urlNewRequest;
        exit;
   }

    // get features

    echo "<H2>Features:</H2><BR>";

    $url1='https://api.viessmann.com/iot/v1/equipment/installations/<ID>/gateways/<Interface ID>/devices/0/features';
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url1,
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
    $result1 = json_decode($response, true);

    $data = $result1['data'];
    foreach ($data as $property) {
        if ($property['isEnabled'] == true) {  
            $status = $property['properties']['status']['value'];
            $value = $property['properties']['value']['value'];            
            $time = $property['timestamp'];
            $feature = $property['feature'];
            echo("<b>");     
            echo("$time : $feature : status = $status, value = $value");
            echo("</b><BR>");
            var_dump($property);
            echo("<BR>");

            if ($feature == 'heating.sensors.volumetricFlow.allengra')
                storeHeatPumpData("VolumeFlow", $time, $value);
            
            if ($feature == 'heating.sensors.temperature.outside')
                storeHeatPumpData("OutsideTemperature", $time, $value);
                
            if ($feature == 'heating.primaryCircuit.sensors.temperature.supply')
                storeHeatPumpData("PrimarySupplyTemperature", $time, $value);

            if ($feature == 'heating.secondaryCircuit.sensors.temperature.supply')
                storeHeatPumpData("SecondarySupplyTemperature", $time, $value);
            
            if ($feature == 'heating.dhw.sensors.temperature.hotWaterStorage')
                storeHeatPumpData("HotWaterTemperature", $time, $value);

            if ($feature == 'heating.circuits.0.sensors.temperature.supply')
                storeHeatPumpData("HeatCircuitSupplyTemperature", $time, $value);
                
            if ($feature == 'heating.sensors.temperature.return')
                storeHeatPumpData("HeatCircuitReturnTemperature", $time, $value);
                
            if ($feature == 'heating.boiler.sensors.temperature.commonSupply')
                storeHeatPumpData("BoilerSupplyTemperature", $time, $value);
                
            if ($feature == 'heating.compressors.0.statistics') {
                $hours = $property['properties']['hours']['value'];
                $starts = $property['properties']['starts']['value']; 
                storeHeatPumpCompressorStatistic("CompressorStatistic", $time, $hours, $starts);
            }
            echo("<BR>&nbsp;</BR>");
       }
    }
    curl_close($curl);

?>