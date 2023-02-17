<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data = json_decode(file_get_contents("php://input"));
  $id_user = $data->id_user;

  session_start();

  if(!empty($_SESSION['id_user']) && $_SESSION['id_user'] == $id_user){
    $_SESSION = array();
    session_destroy();
    echo(
      json_encode([
        'session_ended' => true
      ])
    );
    exit;
  } else {
    echo(
      json_encode([
        'session_ended' => false
      ])
    );
    exit;
  }
?>