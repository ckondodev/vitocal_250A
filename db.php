<?php

/* Datenbank Schema (Tabelle.Column)

 `HeatPumpAccessData`.`accessToken`,
 `HeatPumpAccessData`.`refreshToken`,
 `HeatPumpAccessData`.`ID`,

 `HeatPumpEventCollection`.`Event`,
 `HeatPumpEventCollection`.`Time`,
 `HeatPumpEventCollection`.`EventCode`,
 `HeatPumpEventCollection`.`ID`,

 `HeatPumpTableBoilerSupplyTemperature`.`DateTime`,
 `HeatPumpTableBoilerSupplyTemperature`.`Value`,

 `HeatPumpTableCompressorStatistic`.`DateTime`,
 `HeatPumpTableCompressorStatistic`.`Hours`,
 `HeatPumpTableCompressorStatistic`.`Starts`,

 `HeatPumpTableHeatCircuitReturnTemperature`.`DateTime`,
 `HeatPumpTableHeatCircuitReturnTemperature`.`Value`,

 `HeatPumpTableHeatCircuitSupplyTemperature`.`DateTime`,
 `HeatPumpTableHeatCircuitSupplyTemperature`.`Value`,

 `HeatPumpTableHotWaterTemperature`.`DateTime`,
 `HeatPumpTableHotWaterTemperature`.`Value`, 

 `HeatPumpTableOutsideTemperature`.`DateTime`, 
 `HeatPumpTableOutsideTemperature`.`Value`,

 `HeatPumpTablePrimarySupplyTemperature`.`DateTime`, 
 `HeatPumpTablePrimarySupplyTemperature`.`Value`, 

 `HeatPumpTableSecondarySupplyTemperature`.`DateTime`, 
 `HeatPumpTableSecondarySupplyTemperature`.`Value`, 

 `HeatPumpTableVolumeFlow`.`DateTime`, 
 `HeatPumpTableVolumeFlow`.`Value`
*/

    include('database/config.php');

    // Funktion zur Ausgabe eines Arrays in eine Datei     
    function logDataIntoFile($ArrayToLog, $FilenameForLogdata)
    {
        $handleLog = fopen($FilenameForLogdata, "wt"); 
        fwrite($handleLog, var_export($ArrayToLog, true));
        fclose($handleLog);
    }

    function GetPDO()
    {
        $config = ReadConfig();    
        $db = $config["db"];
        $pdo = new PDO($db['connection'].';dbname='.$db['name'], $db['user'], $db['Password']);
        return $pdo;
    }

    // Funktion zum speichern der Token in der Datenbank
    function storeTokens($accessTok, $refreshTok)
    {
        $sql = "UPDATE HeatPumpAccessData SET `accessToken` = '$accessTok', `refreshToken` = '$refreshTok' WHERE (`ID` = 1);";

        $pdo = GetPDO();
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();
    }

    // funktion gibt das aktuelle AccessToken aus der Datenbank zurück
    function getAccessTokenfromDB()
    {
        $sql = "SELECT DISTINCT `accessToken` FROM HeatPumpAccessData WHERE `ID` = 1";

        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();
        return $statement1->fetch(PDO::FETCH_ASSOC)['accessToken'];
    }

    // funktion gibt das aktuelle refreshToken aus der Datenbank zurück
    function getRefreshTokenfromDB()
    {
        $sql = "SELECT DISTINCT `refreshToken` FROM HeatPumpAccessData WHERE `ID` = 1";

        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();
        return $statement1->fetch(PDO::FETCH_ASSOC)['refreshToken'];
    }

    // Funktion zum speichern eines Events
    function storeHeatPumpEvent($time, $code, $jsonEvent)
    {
        $id = $time.$code;
        //$sql = "INSERT INTO `HeatPumpEventCollection` (`Event`,`Time`,`EventCode`,`ID`) VALUES (`$jsonEvent`, `$time`, `$code`, `$id`)";

        $sql = implode(" ", [
            "INSERT INTO HeatPumpEventCollection (`Event`,`Time`,`EventCode`,`ID`) ",
            "VALUES ('$jsonEvent', '$time', '$code', '$id');"
        ]);
        echo ($sql);
        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();
    }
    
    // Funktion zum speichern der HeatPump Daten
    function storeHeatPumpData($property, $time, $value)
    {
        $table = "HeatPumpTable".$property;
        $sql = implode(" ",[
            "INSERT INTO $table (`DateTime`, `Value`) ",
            "VALUES ('$time', $value);"
        ]);
        echo ($sql);
        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();
    }

    // Funktion zum speichern der Compressor statistic
    function storeHeatPumpCompressorStatistic($property, $time, $hours, $starts)
    {
        $table = "HeatPumpTable".$property;
        $sql = implode(" ",[
            "INSERT INTO $table (`DateTime`, `Hours`, `Starts`) ",
            "VALUES ('$time', $hours, $starts);"
        ]);
        echo ($sql);
        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();
    }
    
?>