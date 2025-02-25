<?php
  $email = $_POST['email'];
  $password = $_POST['password'];

  $con = new mysqli("localhost","root","","registration");
  if($con ->connect_error) {
    die("Connection failed: " . $con->connect_error);
  }else{
    $stmt = $con -,
  }
?>