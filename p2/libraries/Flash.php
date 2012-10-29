<?php

#
# Rudimentary implementation of a rails-style flash to pass messages between redirects.
# XXX:  This implements flash but not flash.now
#

class Flash
{
  # Set a message to be displayed on the next page load.
  public static function set($msg) 
  {
    $_SESSION["flash_set"] = true;
    $_SESSION["flash_msg"] = $msg;
  }

  # Get any messages set during the previous request.
  public static function get() 
  {
    return $_SESSION["flash_msg"];
  }

  # Called at the beginning of each request.  Clears the flash if no messages were set during the previous request.
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

