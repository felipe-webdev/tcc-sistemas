<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data = json_decode(file_get_contents("php://input"));
  $id_user = $data->id_user;

  require_once "dbconnect.php";

  $sql = "UPDATE usuarios SET senha = ? WHERE id = ?;";
  try {
    $statement = $PDO->prepare($sql);
    $statement->execute([password_hash('mestre123', PASSWORD_DEFAULT), $id_user]);
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

  if($statement->rowCount()){
    echo(
      json_encode([
        'success'  => true,
        'affected' =>$statement->rowCount()
      ])
    );
    exit;
  } else {
    echo(
      json_encode([
        'success' => false,
        'error'   => 'no_row_affected'
      ])
    );
    exit;
  }
?>