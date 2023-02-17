<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data     = json_decode(file_get_contents("php://input"));
  $employee = $data->employee;

  require_once "dbconnect.php";

  $sql1 = "INSERT INTO pessoas (
    id_tipo,
    id_entidade,
    nome,
    sobrenome,
    nascimento,
    cpf)
  VALUES( ?, ?, ?, ?, ?, ? );";

  try {
    $statement = $PDO->prepare($sql1);
    $success   = $statement->execute([
      $employee->person_type,
      $employee->id_entity,
      $employee->name,
      $employee->surname,
      $employee->birth_y.'-'.$employee->birth_m.'-'.$employee->birth_d,
      $employee->cpf
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

    $sql2 = "INSERT INTO contatos (
      id_tipo,
      id_pessoa,
      contato)
    VALUES ( ?, ?, ? );";

    try {
      $statement = $PDO->prepare($sql2);
      if($employee->phone){$statement->execute([1, $id_person, $employee->phone]);}
      if($employee->email){$statement->execute([2, $id_person, $employee->email]);}
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

    $sql3 = "INSERT INTO enderecos (
      id_tipo,
      id_pessoa,
      logradouro,
      numero,
      complemento_numero,
      bairro,
      cidade,
      estado,
      uf,
      cep)
    VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? );";

    try {
      $statement = $PDO->prepare($sql3);
      $statement->execute([
        $employee->address->address_type,
        $id_person,
        $employee->address->line,
        $employee->address->number,
        $employee->address->number_info,
        $employee->address->district,
        $employee->address->city,
        null,
        $employee->address->uf,
        $employee->address->cep
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

    $sql4 = "INSERT INTO funcionarios (
      id_pessoa,
      id_cargo,
      ativo)
    VALUES ( ?, ?, ? );";

    try {
      $statement = $PDO->prepare($sql4);
      $success2  = $statement->execute([
        $id_person,
        $employee->job_type,
        1 /* FUNCIONÁRIO ATIVO */
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

    if($success2){
      $id_employee = $PDO->lastInsertId();

      if(sizeof($employee->family) > 0){
        $sql5 = "INSERT INTO dependentes (
          id_tipo,
          id_pessoa,
          id_funcionario)
        VALUES ( ?, ?, ? );";

        for($i = 0; $i < sizeof($employee->family); $i++){
          try {
            $statement = $PDO->prepare($sql1); /* INSERT EM PESSOAS SQL1 */
            $success3  = $statement->execute([
              1, /* PESSOA FÍSICA */
              $employee->id_entity,
              $employee->family[$i]->name,
              $employee->family[$i]->surname,
              $employee->family[$i]->birth_y.'-'.$employee->family[$i]->birth_m.'-'.$employee->family[$i]->birth_d,
              $employee->family[$i]->cpf
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

          if($success3){
            $id_person2 = $PDO->lastInsertId();

            try {
              $statement = $PDO->prepare($sql5); /* INSERT EM DEPENDENTES SQL5 */
              $statement->execute([
                $employee->family[$i]->id_family_type,
                $id_person2,
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

      if($employee->user->create){
        $sql6 = "INSERT INTO usuarios (
          id_grupo,
          id_funcionario,
          usuario,
          senha)
        VALUES ( ?, ?, ?, ? );";

        try {
          $statement = $PDO->prepare($sql6);
          $statement->execute([
            $employee->user->id_user_group,
            $id_employee,
            $employee->user->user.''.$employee->user->postfix,
            password_hash($employee->user->pass, PASSWORD_DEFAULT)
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
  }
?>