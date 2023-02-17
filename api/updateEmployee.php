<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data     = json_decode(file_get_contents("php://input"));
  $new_data = $data->new_data;
  $part     = $data->part;

  require_once "dbconnect.php";

  switch($part){
    case 'personal':
      $sql = "UPDATE pessoas 
      SET 
        nome       = ?,
        sobrenome  = ?,
        nascimento = ?,
        cpf        = ?
      WHERE id     = ?;";
      try {
        $statement = $PDO->prepare($sql); /* UPDATE EM PESSOAS SQL */
        $statement->execute([
          $new_data->name,
          $new_data->surname,
          $new_data->birth_y.'-'.$new_data->birth_m.'-'.$new_data->birth_d,
          $new_data->cpf,
          $new_data->id_person
        ]);
      } catch ( PDOException $e ){
        echo(
          json_encode([
            'success' => false,
            'error'   => 'sql',
            'msg'     => $e->getMessage()
          ])
        );
        break;
      }
      echo(
        json_encode([
          'success' => true
        ])
      );
      break;

    case 'address':
      $sql = "UPDATE enderecos 
      SET 
        logradouro         = ?,
        numero             = ?,
        complemento_numero = ?,
        bairro             = ?,
        cidade             = ?,
        uf                 = ?,
        cep                = ?
      WHERE id             = ?;";
      try {
        $statement = $PDO->prepare($sql); /* UPDATE EM ENDEREÇOS SQL */
        $statement->execute([
          $new_data->line,
          $new_data->number,
          $new_data->number_info,
          $new_data->district,
          $new_data->city,
          $new_data->uf,
          $new_data->cep,
          $new_data->id_address,
        ]);
      } catch ( PDOException $e ){
        echo(
          json_encode([
            'success' => false,
            'error'   => 'sql',
            'msg'     => $e->getMessage()
          ])
        );
        break;
      }
      echo(
        json_encode([
          'success' => true
        ])
      );
      break;

    case 'job':
      $sql = "UPDATE funcionarios 
      SET 
        id_cargo = ?
      WHERE id   = ?;";
      try {
        $statement = $PDO->prepare($sql); /* UPDATE EM FUNCIONÁRIOS SQL */
        $statement->execute([
          $new_data->id_job_type,
          $new_data->id_employee
        ]);
      } catch ( PDOException $e ){
        echo(
          json_encode([
            'success' => false,
            'error'   => 'sql',
            'msg'     => $e->getMessage()
          ])
        );
        break;
      }
      echo(
        json_encode([
          'success' => true
        ])
      );
      break;

      case 'active':
        $sql = "UPDATE funcionarios 
        SET 
          ativo = ?
        WHERE id   = ?;";
        try {
          $statement = $PDO->prepare($sql); /* UPDATE EM FUNCIONÁRIOS SQL */
          $statement->execute([
            $new_data->active,
            $new_data->id_employee
          ]);
        } catch ( PDOException $e ){
          echo(
            json_encode([
              'success' => false,
              'error'   => 'sql',
              'msg'     => $e->getMessage()
            ])
          );
          break;
        }
        echo(
          json_encode([
            'success' => true
          ])
        );
        break;
  }
?>