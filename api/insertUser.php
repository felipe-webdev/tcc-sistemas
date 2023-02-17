<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data        = json_decode(file_get_contents("php://input"));
  $id_employee = $data->id_employee;
  $user        = $data->user;

  require_once "dbconnect.php";

  $sql = "INSERT INTO usuarios (
    id_grupo,
    id_funcionario,
    usuario,
    senha)
  VALUES ( ?, ?, ?, ? );";

  try {
    $statement = $PDO->prepare($sql);
    $statement->execute([
      $user->id_user_group,
      $id_employee,
      $user->user.''.$user->postfix,
      password_hash($user->pass, PASSWORD_DEFAULT)
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