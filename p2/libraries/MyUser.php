<?php

class MyUser extends User {

  public $errors;

  public function __construct() 
  {
    parent::__construct();
    $this->errors = [];
  } 

  // follow/unfollow
  public function follow($user_id)
  {
    $sql = sprintf("select count(1) from users where user_id=%s", $user_id);
    $result = DB::instance(DB_NAME)->select_field($sql);	
    if($result > 0) {
      $attrs = Array(
        "user_id" => $user_id,
        "follower_id" => $this->_user->user_id,
        "stream_id" => 0
      );
      $result = DB::instance(DB_NAME)->insert("users_followers", $attrs);
      echo "alert('You are now following.')";
    } else {
      # XXX error - no such user
      header('HTTP/1.1 500 Internal Server Error');
      #echo "no such user " . $user_id;
    }
  }

  public function unfollow($user_id)
  {
  }

  // accessors
  public function posts()
  {
    $sql = sprintf("select * from posts where user_id=%s", $this->_user->user_id); 
    $posts = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $posts;
  }

  // validation

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
    $this->errors = [];
    $this->validates_length_of($data, "last_name", 1, 50);
    $this->validates_length_of($data, "first_name", 1, 50);
    $this->validates_length_of($data, "email", 8, 50);
    $this->validates_length_of($data, "password", 8, 50);

    $sql = sprintf("select 1 from users where email='%s'", $data["email"]);
    if( DB::instance(DB_NAME)->select_field($sql) ) {
      $this->errors["email"] = "email address is taken";
    }
    return empty($this->errors);
  }

  // statics
  public static function users()
  {
    $sql = "select user_id, first_name, last_name from users order by last_name asc, first_name asc"; 
    $users = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $users;
  }

}
