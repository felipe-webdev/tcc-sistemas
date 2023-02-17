<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data        = json_decode(file_get_contents("php://input"));
  $id_entity   = $data->id_entity;
  $id_employee = $data->id_employee;
  $family      = $data->family;

  require_once "dbconnect.php";

  $sql1 = "INSERT INTO pessoas (
    id_tipo,
    id_entidade,
    nome,
    sobrenome,
    nascimento,
    cpf)
  VALUES( ?, ?, ?, ?, ?, ? );";

  $sql2 = "INSERT INTO dependentes (
    id_tipo,
    id_pessoa,
    id_funcionario)
  VALUES ( ?, ?, ? );";

  if(sizeof($family) > 0){
    for($i = 0; $i < sizeof($family); $i++){
      try {
        $statement = $PDO->prepare($sql1); /* INSERT EM PESSOAS SQL1 */
        $success   = $statement->execute([
          1, /* PESSOA FÍSICA */
          $id_entity,
          $family[$i]->name,
          $family[$i]->surname,
          $family[$i]->birth_y.'-'.$family[$i]->birth_m.'-'.$family[$i]->birth_d,
          $family[$i]->cpf
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

      if($success){
        $id_person = $PDO->lastInsertId();

        try {
          $statement = $PDO->prepare($sql2); /* INSERT EM DEPENDENTES SQL2 */
          $statement->execute([
            $family[$i]->id_family_type,
            $id_person,
            $id_employee
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
  }

  echo(
    json_encode([
      'success' => true
    ])
  );
  exit;
?>