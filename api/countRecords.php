<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: GET");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  require_once "dbconnect.php";

  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM pessoas            AS p
  INNER JOIN funcionarios AS f
    ON f.id_pessoa = p.id;");
  $total = $result->fetchAll();


  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM pessoas            AS p
  INNER JOIN funcionarios AS f
    ON f.id_pessoa = p.id
  WHERE f.ativo = 1;");
  $active = $result->fetchAll();


  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM pessoas            AS p
  INNER JOIN funcionarios AS f
    ON f.id_pessoa = p.id
  WHERE f.ativo = 0;");
  $inactive = $result->fetchAll();


  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM dependentes        AS d
  INNER JOIN funcionarios AS f
    ON f.id = d.id_funcionario;");
  $total_family = $result->fetchAll();


  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM dependentes        AS d
  INNER JOIN funcionarios AS f
    ON f.id = d.id_funcionario
  WHERE f.ativo = 1;");
  $active_family = $result->fetchAll();


  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM dependentes        AS d
  INNER JOIN funcionarios AS f
    ON f.id = d.id_funcionario
  WHERE f.ativo = 0;");
  $inactive_family = $result->fetchAll();


  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM usuarios           AS u
  INNER JOIN funcionarios AS f
    ON f.id = u.id_funcionario;");
  $total_user = $result->fetchAll();


  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM usuarios           AS u
  INNER JOIN funcionarios AS f
    ON f.id = u.id_funcionario
  WHERE f.ativo = 1;");
  $active_user = $result->fetchAll();


  $result = $PDO->query("SELECT
    COUNT(*)              AS total
  FROM usuarios           AS u
  INNER JOIN funcionarios AS f
    ON f.id = u.id_funcionario
  WHERE f.ativo = 0;");
  $inactive_user = $result->fetchAll();


  echo(
    json_encode([
      'employee'   =>[
        'total'    =>$total[0]['total'],
        'active'   =>$active[0]['total'],
        'inactive' =>$inactive[0]['total']
      ],
      'family'     =>[
        'total'    =>$total_family[0]['total'],
        'active'   =>$active_family[0]['total'],
        'inactive' =>$inactive_family[0]['total']
      ],
      'user'       =>[
        'total'    =>$total_user[0]['total'],
        'active'   =>$active_user[0]['total'],
        'inactive' =>$inactive_user[0]['total']
      ]
    ])
  );
  exit;
?>