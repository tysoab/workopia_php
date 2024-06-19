<?php

class Database
{
  public $conn;

  /**
   * constructor for Database class
   *
   * @param array $config
   */
  public function __construct($config)
  {
    $dns = "mysql:host{$config['host']};port={$config['port']};dbname={$config['dbname']};";

    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      // fetch mode
      // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    try {
      $this->conn = new PDO($dns, $config['username'], $config['password'], $options);
    } catch (PDOException $e) {
      throw new Exception("Database connection failed: {$e->getMessage()}");
    }
  }

  /**
   * Query the databse
   *
   * @param string $query
   * @return PDOStatement
   * @throws PDOException
   */
  // modify query to take params
  // public function query($query)
  public function query($query, $params = [])
  {
    try {
      $sth = $this->conn->prepare($query);
      // bind name params
      foreach ($params as $param => $value) {
        $sth->bindValue(':' . $param, $value);
      }

      $sth->execute();
      return $sth;
    } catch (PDOException $e) {
      throw new Exception("Query failed to  execute: {$e->getMessage()}");
    }
  }
}
