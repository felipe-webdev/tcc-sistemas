<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data  = json_decode(file_get_contents("php://input"));
  $job = $data->job;

  require_once "dbconnect.php";

  $sql = "UPDATE cargos 
  SET 
    id_area    = ?,
    nome       = ?,
    salario    = ?
  WHERE id     = ?;";

  try {
    $statement = $PDO->prepare($sql); /* UPDATE EM CONTATOS SQL */
    $statement->execute([
      $job->id_job_depart,
      $job->job_type,
      $job->salary,
      $job->id_job_type
    ]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'success' => false,
        'error'   => 'sql',
        'msg'     => $e->getMessage()
      ])
    );
    exit;
  }

  echo(
    json_encode([
      'success' => true
    ])
  );
  exit;
?>