<?php

    function ReadConfig()
    {
        return [
            'db' => [
                'name' => 'db............',
                'user' => 's...........',
                'Password' => '............',
                'connection' => 'mysql:host=<IP oder URL>',
                'options'=> [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
               ]
            ]
        ];
    }

?>