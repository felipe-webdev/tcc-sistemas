<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data      = json_decode(file_get_contents("php://input"));
  $id_person = $data->id_person;
  $phones    = $data->phones;

  require_once "dbconnect.php";

  $sql = "INSERT INTO contatos (
    id_tipo,
    id_pessoa,
    contato,
    observacao)
  VALUES( ?, ?, ?, ? );";

  if(sizeof($phones) > 0){
    for($i = 0; $i < sizeof($phones); $i++){
      try {
        $statement = $PDO->prepare($sql); /* INSERT EM CONTATOS SQL */
        $statement->execute([
          $phones[$i]->id_contact_type,
          $id_person,
          $phones[$i]->phone,
          $phones[$i]->obs
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
    }
  }

  echo(
    json_encode([
      'success' => true
    ])
  );
  exit;
?>