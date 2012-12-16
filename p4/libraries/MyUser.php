<?php

class MyUser extends User {

  public $errors;

  public function __construct() 
  {
    parent::__construct();
    $this->errors = Array();
  } 

  public function __load_user() 
  {
    $data = parent::__load_user(); 
    if($data) {
      # load additional info if authentication succeeded!
    }
    return $data;
  }

  ############################################################
  # associations

  ############################################################
  # validation

  private function validates_presence_of($data, $attr, $msg=null)
  {
    if(empty($data[$attr])) {
      if(!$msg) { $msg = $attr . " is required"; }
      $this->errors[$attr] = $msg; 
    }
  }

  private function validates_length_of($data, $attr, $min, $max, $msg=null)
  {
    if(empty($data[$attr])) {
      $len = 0;
    } else {
      $len = strlen($data[$attr]);
    }
    if($len < $min || $len > $max ) {
      if(!$msg) { 
        $msg = $attr . " should be between " . $min . " and " . $max . " characters"; 
      }
      $this->errors[$attr] = $msg; 
    }
  }

  public function valid($data) 
  {
    $data = DB::instance(DB_NAME)->sanitize($data);

    $is_new_user = !isset($this->_user->user_id);
    $this->errors = Array();
    if($is_new_user || isset($data["last_name"])) {
      $this->validates_length_of($data, "last_name", 1, 50);
    }
    if($is_new_user || isset($data["first_name"])) {
      $this->validates_length_of($data, "first_name", 1, 50);
    }
    if($is_new_user || isset($data["email"])) {
      $this->validates_length_of($data, "email", 8, 50);
      $sql = sprintf("select 1 from users where email='%s'", $data["email"]);
      if( DB::instance(DB_NAME)->select_field($sql) ) {
        $this->errors["email"] = "email address is taken";
      }
    }
    if($is_new_user || isset($data["password"])) {
      $this->validates_length_of($data, "password", 8, 50);
    }
    return empty($this->errors);
  }

  public function error_message()
  {
    $msg = "";
    if(!empty($this->errors)) {
      $msg = join(". ", $this->errors);
    }
    return $msg;
  }

  public function update($data)
  {
    # convert checkboxes from "on" to 0 or 1
    $attrs = Array("publish_content", "use_external_content");
    foreach($attrs as $attr) {
      if(isset($data[$attr]) && $data[$attr] == "on") {
        $data[$attr] = 1; 
      } else {
        $data[$attr] = 0; 
      }
    }
    # remove values that are not being updated
    if(empty($data["password"])) {
      unset($data["password"]);
      unset($data["password_confirm"]);
    }
    $attrs = Array("first_name", "last_name", "email", "publish_content", "use_external_content");
    foreach($attrs as $attr) {
      if(isset($data[$attr]) && $data[$attr] == $this->_user->$attr) {
        unset($data[$attr]);
      }
    }
    if(!$this->valid($data)) {
      return false;
    } 
    if(isset($data["password"])) {
      $data["password"] = sha1(PASSWORD_SALT.$_POST['password']);
      unset($data["password_confirm"]); 
    }
    # is there anything left to update?
    if(!empty($data)) {
      DB::instance(DB_NAME)->update("users", $data, "WHERE user_id = " . $this->_user->user_id);
    }
    return true;
  }

  ############################################################
  # statics
  public static function users()
  {
    $sql = "select user_id, first_name, last_name from users order by last_name asc, first_name asc"; 
    $users = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $users;
  }

  public static function user_exists($user_id) 
  {
    $sql = sprintf("select count(1) from users where user_id=%d", $user_id);
    $result = DB::instance(DB_NAME)->select_field($sql);
    return($result > 0);
  }

  public static function full_name($user)
  {
    return htmlspecialchars($user->first_name . " " . $user->last_name);
  }

  public static function public_user_info_for($user_id)
  {
    # profile information
    $sql = sprintf("select user_id, first_name, last_name from users where user_id=%d", $user_id);
    $result = DB::instance(DB_NAME)->select_row($sql, "object");

    return($result);
  }

}
