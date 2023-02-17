<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data          = json_decode(file_get_contents("php://input"));
  $name          = "%$data->name%";
  $id_job_depart = $data->id_job_depart;
  $page          = $data->page;
  $page_limit    = $data->page_limit;
  $page_offset   = 0;
  if($page > 0){ $page_offset = $page * $page_limit; }

  require_once "dbconnect.php";

  $sql1="SELECT 
    COUNT(*) AS `count`
  FROM (
    SELECT id
    FROM   cargos
    WHERE  nome LIKE ?";

  if($id_job_depart){
    $sql1=$sql1."
    AND id_area = ?";
  }

  $sql1=$sql1."
    ) AS jobs;";

  $param = [$name];
  if($id_job_depart){ $param[] = $id_job_depart; }

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
      id         AS id_job_type,
      id_area    AS id_job_depart,
      nome       AS job_type,
      salario    AS salary
    FROM cargos
    WHERE nome LIKE ?";

    if($id_job_depart){
      $sql2=$sql2."
      AND id_area = ?";
    }

    $sql2=$sql2."
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