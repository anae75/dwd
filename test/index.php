<?php 
 require_once 'config.php'; 
 Print "Hello, World!";
 Print "It is " . date(DATE_RSS, time());

  $con = mysql_connect("localhost",DB_USER,DB_PASSWD);
  if (!$con)
    {
    die('Could not connect: ' . mysql_error());
  } else {
    Print("I was able to connect to mysql!");
  }
  mysql_close($con);

 // phpinfo(); 
 ?> 


