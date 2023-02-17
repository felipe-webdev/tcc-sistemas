<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data      = json_decode(file_get_contents("php://input"));
  $id_family = $data->id_family;
  $id_person = $data->id_person;

  require_once "dbconnect.php";

  $sql1 = "DELETE FROM dependentes WHERE id = ?;";

  try {
    $statement = $PDO->prepare($sql1); /* DELETE EM DEPENDENTES SQL1 */
    $statement->execute([
      $id_family
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

  $sql2 = "DELETE FROM pessoas WHERE id = ?;";

  try {
    $statement = $PDO->prepare($sql2); /* DELETE EM PESSOAS SQL2 */
    $statement->execute([
      $id_person
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