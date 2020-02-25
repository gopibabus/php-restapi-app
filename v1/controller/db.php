<?php
  require_once('../../vendor/autoload.php');

  use Symfony\Component\Yaml\Parser;

  /**
   * Class to organize all database connections
   * Class DB
   */
  class DB
  {
    private static $writeDBConnection;

    private static $readDBConnection;

    /**
     * Db responsible for writing data to database
     * @return PDO
     */
    public static function connectWriteDB()
    {
      $yaml = new Parser();
      $config = $yaml->parse(file_get_contents('../../config.yaml'));

      $host = $config['write-db']['host'];
      $db = $config['write-db']['db-name'];
      $charset = $config['write-db']['charset'];
      $user = $config['write-db']['username'];
      $pass = $config['write-db']['password'];

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

    /**
     * Db responsible for reading data
     * @return PDO
     */
    public static function connectReadDB()
    {
      $yaml = new Parser();
      $config = $yaml->parse(file_get_contents('../../config.yaml'));

      $host = $config['read-db']['host'];
      $db = $config['read-db']['db-name'];
      $charset = $config['read-db']['charset'];
      $user = $config['read-db']['username'];
      $pass = $config['read-db']['password'];

      $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ];
      if (self::$readDBConnection == null) {
        self::$readDBConnection =
          new PDO($dsn, $user, $pass, $options);
      }

      return self::$readDBConnection;
    }
  }