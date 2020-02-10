<?php


class DB
{
    private static $writeDBConnection;

    private static $readDBConnection;

    public static function connectWriteDB()
    {
        $host = '165.227.204.253';
        $db = 'tasks_db';
        $charset = 'utf8mb4';
        $user = 'harika_ammu';
        $pass = 'harika_ammu';
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        if (self::$writeDBConnection == null) {
            self::$writeDBConnection =
                new PDO($dsn, $user, $pass, $options);
        }

        return self::$writeDBConnection;
    }

    public static function connectReadDB()
    {
        if (self::$readDBConnection == null) {
            self::$readDBConnection =
                new PDO('mysql:host=165.227.204.253;dbname=tasks_db;charset=utf8',
                    'gopibabu', 'gopibabu'
                );
            self::$readDBConnection->
            setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$readDBConnection->
            setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }

        return self::$readDBConnection;
    }

}