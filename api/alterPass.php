<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data     = json_decode(file_get_contents("php://input"));
  $id_user  = $data->id_user;
  $old_pass = $data->old_pass;
  $new_pass = $data->new_pass;

  require_once "dbconnect.php";

  $sql = "SELECT senha AS pass FROM usuarios WHERE id = ?;";
  try {
    $statement = $PDO->prepare($sql);
    $statement->execute([$id_user]);
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
  $result = $statement->fetch();

  if(password_verify($new_pass, $result['pass'])){
    echo(
      json_encode([
        'success' => false,
        'error'   => 'new_pass'
      ])
    );
    exit;
  }

  if(password_verify($old_pass, $result['pass'])){
    $sql2 = "UPDATE usuarios SET senha = ? WHERE id = ?;";
    try {
      $statement2 = $PDO->prepare($sql2);
      $statement2->execute([password_hash($new_pass, PASSWORD_DEFAULT), $id_user]);
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

    if($statement2->rowCount()){
      echo(
        json_encode([
          'success'  => true,
          'affected' =>$statement2->rowCount()
        ])
      );
      exit;
    }
  } else {
    echo(
      json_encode([
        'success' => false,
        'error'   => 'old_pass'
      ])
    );
    exit;
  }
?>