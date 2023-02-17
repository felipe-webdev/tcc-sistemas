<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data   = json_decode(file_get_contents("php://input"));
  $family = $data->family;

  require_once "dbconnect.php";

  $sql1 = "UPDATE pessoas
    SET
      nome       = ?,
      sobrenome  = ?,
      nascimento = ?,
      cpf        = ?
    WHERE id     = ?;";

  try {
    $statement = $PDO->prepare($sql1); /* UPDATE EM PESSOAS SQL1 */
    $statement->execute([
      $family->name,
      $family->surname,
      $family->birth_y.'-'.$family->birth_m.'-'.$family->birth_d,
      $family->cpf,
      $family->id_person
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

  $sql2 = "UPDATE dependentes SET id_tipo = ? WHERE id = ?;";

  try {
    $statement = $PDO->prepare($sql2); /* UPDATE EM DEPENDENTES SQL2 */
    $statement->execute([
      $family->id_family_type,
      $family->id_family
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