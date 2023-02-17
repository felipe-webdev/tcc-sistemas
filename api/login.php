<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data = json_decode(file_get_contents("php://input"));
  $user = $data->user;
  $pass = $data->pass;

  require_once "dbconnect.php";

  $sql = "SELECT COUNT(id) AS is_user FROM usuarios WHERE usuario = ?;";
  try {
    $statement = $PDO->prepare($sql);
    $statement->execute([$user]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'session_started' => false,
        'error'           => 'sql',
        'msg'             => $e->getMessage()
      ])
    );
    exit;
  }
  $result = $statement->fetch();

  if($result['is_user'] === 1){
    $sql0 = "SELECT 
      ativo 
    FROM funcionarios AS f 
    INNER JOIN usuarios AS u 
      ON u.id_funcionario = f.id
    WHERE u.usuario = ?;";
    try {
      $statement0 = $PDO->prepare($sql0);
      $statement0->execute([$user]);
    } catch ( PDOException $e ){
      echo(
        json_encode([
          'session_started' => false,
          'error'           => 'sql',
          'msg'             => $e->getMessage()
        ])
      );
      exit;
    }
    $result0 = $statement0->fetch();
    if($result0['ativo'] === 0){
      echo(
        json_encode([
          'session_started' => false,
          'error'           => 'active',
        ])
      );
      exit;
    }
  }

  if($result['is_user'] === 1) {
    $sql2 = "SELECT senha AS pass FROM usuarios WHERE usuario = ?;";
    try {
      $statement2 = $PDO->prepare($sql2);
      $statement2->execute([$user]);
    } catch ( PDOException $e ){
      echo(
        json_encode([
          'session_started' => false,
          'error'           => 'sql',
          'msg'             => $e->getMessage()
        ])
      );
      exit;
    }
    $result2 = $statement2->fetch();

    if(password_verify($pass, $result2['pass'])){
      $sql3 = "SELECT 
        u.id                       AS id_user,
        u.usuario                  AS user,
        u.id_funcionario           AS id_employee,
        u.id_grupo                 AS id_group,
        g.nome                     AS `group`,
        p.id                       AS id_person,
        p.nome                     AS name_user,
        p.sobrenome                AS surname_user,
        e.id                       AS id_entity,
        e.nome                     AS entity,
        e.posfixo_usuario          AS postfix
        FROM usuarios              AS u
        INNER JOIN usuarios_grupos AS g
          ON g.id = u.id_grupo
        INNER JOIN funcionarios    AS f
          ON f.id = u.id_funcionario
        INNER JOIN pessoas         AS p
          ON p.id = f.id_pessoa
        INNER JOIN entidades       AS e
          ON e.id = p.id_entidade
        WHERE u.usuario = ?;";
      try {
        $statement3 = $PDO->prepare($sql3);
        $statement3->execute([$user]);
      } catch ( PDOException $e ){
        echo(
          json_encode([
            'session_started' => false,
            'error'           => 'sql',
            'msg'             => $e->getMessage()
          ])
        );
        exit;
      }
      $result3 = $statement3->fetch();

      session_start();
      $_SESSION['authenticated'] = true;
      $_SESSION['id_user']       = $result3['id_user'];
      $_SESSION['user']          = $result3['user'];
      $_SESSION['id_employee']   = $result3['id_employee'];
      $_SESSION['id_group']      = $result3['id_group'];
      $_SESSION['group']         = $result3['group'];
      $_SESSION['id_person']     = $result3['id_person'];
      $_SESSION['name_user']     = $result3['name_user'];
      $_SESSION['surname_user']  = $result3['surname_user'];
      $_SESSION['id_entity']     = $result3['id_entity'];
      $_SESSION['entity']        = $result3['entity'];
      $_SESSION['postfix']       = $result3['postfix'];

      echo(
        json_encode([
          'session_started' => true
        ])
      );
      exit;
    } else {
      echo(
        json_encode([
          'session_started' => false,
          'error'           => 'pass'
        ])
      );
      exit;
    }
  } else {
    echo(
      json_encode([
        'session_started' => false,
        'error'           => 'user'
      ])
    );
    exit;
  }
?>