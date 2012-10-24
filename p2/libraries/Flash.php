<?php

class Flash
{
  public static function set($msg) 
  {
    $_SESSION["flash_set"] = true;
    $_SESSION["flash_msg"] = $msg;
  }

  public static function get() 
  {
    return $_SESSION["flash_msg"];
  }

  public static function init()
  {
    if(!isset($_SESSION["flash_set"]) || !$_SESSION["flash_set"]) {
      $_SESSION["flash_msg"] = "";
    }
    $_SESSION["flash_set"] = false;
  }

  public static function has_message()
  {
    return !empty($_SESSION["flash_msg"]);
  }

}

