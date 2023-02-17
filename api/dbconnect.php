<?php
  require_once "dbconfig.php";

  $PDO_OPTIONS = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];

  $DSN = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;

  try
  {
    $PDO = new PDO( $DSN, DB_USER, DB_PASSWORD, $PDO_OPTIONS );
  }
  catch ( PDOException $e )
  {
    echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
  }
?>