<?php

class MyUser extends User {

  public $errors;

  public function __construct() 
  {
    parent::__construct();
    $this->errors = [];
  } 

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

}
