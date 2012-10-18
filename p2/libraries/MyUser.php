<?php

class MyUser extends User {

  public $errors;

  public function __construct() 
  {
    parent::__construct();
    $this->errors = [];
    echo "MyUser construct called<br><br>";
  } 

  private function validates_presence_of($attr, $msg=null)
  {
    if(empty($data[$attr])) {
      if(!$msg) { $msg = $attr . " is required"; }
      $this->errors[$attr] = $msg; 
    }
  }

  private function validates_length_of($attr, $len, $msg=null)
  {
    if(empty($data[$attr] || strlen($data[$attr]) < $len) {
      if(!$msg) { $msg = $attr . " needs to be at least" . $len . " characters"; }
      $this->errors[$attr] = $msg; 
    }
  }

  public function valid($data) 
  {
    $this->errors = [];
    $this->validates_presence_of("email");
    $this->validates_presence_of("password");
    $this->validates_presence_of("first_name");
    $this->validates_presence_of("last_name");
    return empty($this->errors);
  }
	
}
