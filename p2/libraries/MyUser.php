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
    if(MyUser::user_exists($user_id)) {
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

  public static function user_exists($user_id) 
  {
    $sql = sprintf("select count(1) from users where user_id=%s", $user_id);
    $result = DB::instance(DB_NAME)->select_field($sql);
    return($result > 0);
  }

  public static function full_name($user)
  {
    return $user->first_name . " " . $user->last_name;
  }

  public static function public_user_info_for($user_id)
  {
    $sql = sprintf("select user_id, first_name, last_name from users where user_id=%s", $user_id);
    $result = DB::instance(DB_NAME)->select_row($sql, "object");
    return($result);
  }

  # all followers of $user_id
  public static function followers($user_id)
  {
    # why this language can't deal with whitespace before the END_SQL is a mystery
    $sql = <<<END_SQL
      select distinct users.user_id, users.first_name, users.last_name 
      from users_followers uf
        inner join users on users.user_id=uf.follower_id
      where uf.user_id=$user_id
      order by users.last_name asc, users.first_name asc
END_SQL;
    $users = DB::instance(DB_NAME)->select_rows($sql, "object");
    return($users);
  }

  # all users being followed by $user_id
  public static function following($user_id)
  {
    $sql = <<<END_SQL
      select distinct users.user_id, users.first_name, users.last_name 
      from users_followers uf
        inner join users on users.user_id=uf.user_id
      where uf.follower_id=$user_id
      order by users.last_name asc, users.first_name asc
END_SQL;
    $users = DB::instance(DB_NAME)->select_rows($sql, "object");
    return($users);
  }

}
