<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data = json_decode(file_get_contents("php://input"));
  $user = $data->user;

  require_once "dbconnect.php";

  $sql = "SELECT COUNT(id) AS is_user FROM usuarios WHERE usuario = ?;";
  try {
    $statement = $PDO->prepare($sql);
    $statement->execute([$user]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'available' => false,
        'error'     => 'sql',
        'msg'       => $e->getMessage()
      ])
    );
    exit;
  }
  $result = $statement->fetch();

  if($result['is_user'] === 1) {
    echo(
      json_encode([
        'available' => false,
        'error'     => 'user',
        'user'      => $user
      ])
    );
    exit;
  } else {
    echo(
      json_encode([
        'available' => true,
        'user'      => $user
      ])
    );
    exit;
  }
?>