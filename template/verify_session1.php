<?php
  session_start();
  if(!empty($_SESSION["authenticated"])){
    header("location: ./app.php");
    exit;
  }
?>