<?php
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
    
    // Funktion gibt die aktuelle Anzahl der Datensätze aus der ShortTimeTable zurück
    function getShortTimeTableCount()
    {
        $sql = "SELECT COUNT(`ID`) FROM ShortTimeDataCache";

        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();
        return $statement1->fetch(PDO::FETCH_ASSOC);
    }

    function deleteRawDataAfterAveraging()
    {
        $sql = "DELETE from `ShortTimeDataCache` ORDER BY `ID` LIMIT 10";

        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();       
    }


    function getAveragedValues()
    {
        $sql = implode(" ", [
            "SELECT MAX(`utc`),",
            "AVG(`temp_indoor`), AVG(`humidity_indoor`),",
            "AVG(`temp_outdoor`), AVG(`humidity_outdoor`),",
            "AVG(`baro_rel`), AVG(`baro_abs`),",
            "AVG(`windspeed`), MAX(`windgust`), AVG(`winddirection`),",
            "SUM(`actual_rain`),",
            "AVG(`solar_radiation`), AVG(`uv`)",
            "FROM ( Select * from `ShortTimeDataCache` LIMIT 10) as alias",
        ]);

        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute();
        return $statement1->fetch(PDO::FETCH_ASSOC);              
    }

    // Funktion zum Speichern der aktuellen Werte in die ShortTime Tabelle
    function storeDataIntoShortTimeTable($DataToStore)
    {
        $sql = implode(" ", [
            "INSERT INTO ShortTimeDataCache (",
            "`utc`,",
            "`temp_indoor`, `humidity_indoor`,",
            "`temp_outdoor`, `humidity_outdoor`,",
            "`baro_rel`, `baro_abs`,",
            "`windspeed`, `windgust`, `winddirection`,",
            "`actual_rain`,",
            "`solar_radiation`, `uv`",
            ") VALUES (",
            ":date_time,",
            ":indoortemp_c, :indoorhumidity,",
            ":temp_air_2m_0_c, :humidity,",
            ":baromin, :absbaromin,",
            ":windspeed, :windgust, :winddir,",
            ":rainin,",
            ":solar_radiation_0, :uvi_0",
            ")",
        ]);

        $pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
        $statement1->execute($DataToStore);
    }

    // Funktion zum Speichern der aktuellen Werte in die Tabelle
    function storeDataIntoTable($DataToStore)
    {
        $sql = implode(" ", [
            "INSERT INTO StationMetricData (",
            "`date_time`, `date`, `time`,",
            "`temp_air_2m_0_c`, `temp_dewpoint_c`, `temp_wct_c`, `humidity`,",
            "`baromin`, `absbaromin`,",
            "`windspeed`, `windbft`, `windbfttxt`, `windgust`, `gustbft`, `gustbfttxt`, `winddir`,",
            "`rainin`, `dailyrainin`, `weeklyrainin`, `monthlyrainin`, `yearlyrainin`,",
            "`solar_radiation_0`, `uvi_0`, `uvi_txt`,",
            "`indoortemp_c`, `indoorhumidity`,",
            "`sunset`, `sunrise`,",
            "`mondphase`, `winddirkurz`, `winddirlang`",
            ") VALUES (",
            ":date_time, :date, :time,",
            ":temp_air_2m_0_c, :temp_dewpoint_c, :temp_wct_c, :humidity,",
            ":baromin, :absbaromin,",
            ":windspeed, :windbft, :windbfttxt, :windgust, :gustbft, :gustbfttxt, :winddir,",
            ":rainin, :dailyrainin, :weeklyrainin, :monthlyrainin, :yearlyrainin,",
            ":solar_radiation_0, :uvi_0, :uvi_txt,",
            ":indoortemp_c, :indoorhumidity,",
            ":sunset, :sunrise,",
            ":mondphase, :winddirkurz, :winddirlang",
            ")",
        ]);

    	$pdo = GetPDO(); 
        $statement1 = $pdo->prepare($sql);
	    $statement1->execute($DataToStore);
    }

?>