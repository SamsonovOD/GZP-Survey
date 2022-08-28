<?php
  $servername = "localhost";
  $username = "Amon";
  $password = "TestPass2";
  $database = "gzp_survey";
  
  $conn = new mysqli($servername, $username, $password, $database);
  
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  
  session_start();
?>