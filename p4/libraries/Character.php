<?php

class Character {

  public function __construct() 
  {
  }

  public static function create($opts) 
  {
    $id = DB::instance(DB_NAME)->insert("characters", $opts); 
    return $id;
  }


}
