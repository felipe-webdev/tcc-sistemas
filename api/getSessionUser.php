<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: GET");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  session_start();
  if(!empty($_SESSION["authenticated"])){
    echo(
      json_encode([
        'session_started'=> true,
        'session_user'   =>(object)[
          'id_user'      => $_SESSION['id_user'],
          'user'         => $_SESSION['user'],
          'id_employee'  => $_SESSION['id_employee'],
          'id_group'     => $_SESSION['id_group'],
          'group'        => $_SESSION['group'],
          'id_person'    => $_SESSION['id_person'],
          'name_user'    => $_SESSION['name_user'],
          'surname_user' => $_SESSION['surname_user'],
          'id_entity'    => $_SESSION['id_entity'],
          'entity'       => $_SESSION['entity'],
          'postfix'      => $_SESSION['postfix'],
        ]
      ])
    );
    exit;
  } else {
    echo(
      json_encode([
        'session_started' => false
      ])
    );
    exit;
  }
?>