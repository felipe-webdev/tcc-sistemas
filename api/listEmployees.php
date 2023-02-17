<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data        = json_decode(file_get_contents("php://input"));
  $name        = "%$data->name%";
  $job_type    = $data->job_type;
  $active      = $data->active;
  $page        = $data->page;
  $page_limit  = $data->page_limit;
  $page_offset = 0;
  if($page > 0){ $page_offset = $page * $page_limit; }

  require_once "dbconnect.php";

  $sql1="SELECT 
    COUNT(*)                AS `count`
  FROM ( SELECT 
      f.id
    FROM pessoas            AS p
    INNER JOIN funcionarios AS f
      ON f.id_pessoa = p.id
    INNER JOIN cargos       AS c
      ON c.id = f.id_cargo
    LEFT JOIN usuarios      AS u
      ON u.id_funcionario = f.id
    LEFT JOIN dependentes   AS d
      ON d.id_funcionario = f.id
    WHERE CONCAT(p.nome, ' ', p.sobrenome) LIKE ?";

  if($active == 0 || $active == 1){
    $sql1=$sql1."
    AND f.ativo = ?";
  }

  if($job_type){
    $sql1=$sql1."
    AND c.id = ?";
  }

  $sql1=$sql1."
    GROUP BY f.id) AS employees;";

  $param = [$name];
  if($active == 0 || $active == 1){ $param[] = $active; }
  if($job_type){ $param[] = $job_type; }

  try {
    $statement = $PDO->prepare($sql1);
    $statement->execute($param);
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
  $result  = $statement->fetchAll();
  $records = $result[0]['count'];
  $count   = $result[0]['count'];
  $pages   = 0;

  if($count > 0){
    while($count){
      if($count < $page_limit){ $pages++; break; }
      $count = $count - $page_limit;
      $pages++;
    }
  }

  if($pages){
    $sql2="SELECT 
      f.id,
      CONCAT(p.nome, ' ', p.sobrenome) AS `name`,
      CASE
        WHEN u.id IS NULL THEN 'false'
        ELSE 'true'
      END                              AS is_user,
      CASE
        WHEN d.id IS NULL THEN 0
        ELSE (SELECT COUNT(id) FROM dependentes WHERE id_funcionario = f.id)
      END                              AS family,
      (SELECT contato AS phone
        FROM  contatos
        WHERE id_tipo = 1
          AND id_pessoa = p.id
        LIMIT 1)                       AS phone,
      (SELECT contato AS email
        FROM  contatos
        WHERE id_tipo = 2
          AND id_pessoa = p.id
        LIMIT 1)                       AS email,
      c.nome                           AS job,
      f.ativo                          AS active
    FROM pessoas                       AS p
    INNER JOIN funcionarios            AS f
      ON f.id_pessoa = p.id
    INNER JOIN cargos                  AS c
      ON c.id = f.id_cargo
    LEFT JOIN usuarios                 AS u
      ON u.id_funcionario = f.id
    LEFT JOIN dependentes              AS d
      ON d.id_funcionario = f.id
    WHERE CONCAT(p.nome, ' ', p.sobrenome) LIKE ?";

    if($active == 0 || $active == 1){
      $sql2=$sql2."
      AND f.ativo = ?";
    }

    if($job_type){
      $sql2 = $sql2."
      AND c.id = ?";
    }

    $sql2 = $sql2."
      GROUP BY f.id
      ORDER BY p.nome, p.sobrenome
      LIMIT ? OFFSET ?;";

    try {
      $statement = $PDO->prepare($sql2);
      $param[]   = $page_limit;
      $param[]   = $page_offset;
      $statement->execute($param);
    } catch( PDOException $e ){
      echo(
        json_encode([
          'success' => false,
          'error'   => 'sql',
          'msg'     => $e->getMessage()
        ])
      );
      exit;
    }
    $rows = $statement->fetchAll();

    echo(
      json_encode([
        'success'     => true,
        'page'        => $page,
        'page_limit'  => $page_limit,
        'page_offset' => $page_offset,
        'pages'       => $pages,
        'records'     => $records,
        'result'      => $rows
      ])
    );
  } else {
    echo(
      json_encode([
        'success'     => false,
        'error'       => 'empty',
        'page'        => $page,
        'page_limit'  => $page_limit,
        'page_offset' => $page_offset,
        'pages'       => $pages,
        'records'     => $records,
        'result'      => []
      ])
    );
  }
?>