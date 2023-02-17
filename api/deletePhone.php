<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data     = json_decode(file_get_contents("php://input"));
  $id_phone = $data->id_phone;

  require_once "dbconnect.php";

  $sql = "DELETE FROM contatos WHERE id = ?;";

  try {
    $statement = $PDO->prepare($sql); /* DELETE EM CONTATOS SQL */
    $statement->execute([
      $id_phone
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