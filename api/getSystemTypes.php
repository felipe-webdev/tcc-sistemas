<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: GET");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  require_once "dbconnect.php";

  $result = $PDO->query("SELECT 
    descricao AS label,
    id        AS `value`
  FROM pessoas_tipos ORDER BY id;");
  $person = $result->fetchAll();


  $result = $PDO->query("SELECT 
    descricao AS label,
    id        AS `value`
  FROM dependentes_tipos ORDER BY id;");
  $family = $result->fetchAll();


  $result = $PDO->query("SELECT 
    descricao AS label,
    id        AS `value`
  FROM enderecos_tipos ORDER BY id;");
  $address = $result->fetchAll();


  $result = $PDO->query("SELECT 
    descricao AS label,
    id        AS `value`
  FROM contatos_tipos ORDER BY id;");
  $contact = $result->fetchAll();


  $result = $PDO->query("SELECT 
    nome AS label,
    id   AS `value`
  FROM usuarios_grupos ORDER BY id;");
  $user_group = $result->fetchAll();


  $result = $PDO->query("SELECT 
    nome AS label,
    id   AS `value`
  FROM cargos ORDER BY id;");
  $job = $result->fetchAll();


  $result = $PDO->query("SELECT 
    nome AS label,
    id   AS `value`
  FROM areas ORDER BY id;");
  $job_depart = $result->fetchAll();


  echo(
    json_encode([
      'person'     =>$person,
      'family'     =>$family,
      'contact '   =>$contact,
      'address'    =>$address,
      'user_group' =>$user_group,
      'job'        =>$job,
      'job_depart' =>$job_depart
    ])
  );
  exit;
?>