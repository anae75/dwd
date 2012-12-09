<?php

class UploadedImage {

  public $errors;

  public function __construct() 
  {
  }

  public static function create($img, $opts) 
  {
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $img_data = base64_decode($img);

    $img_id = DB::instance(DB_NAME)->insert("images", $opts); 
    $opts = Array();
    $opts["filename"] = sprintf("images/uu_%d.png", $img_id); 
    DB::instance(DB_NAME)->update("images", $opts, "where id=".$img_id); 
    $success = file_put_contents($opts["filename"], $img_data);
    return $img_id;
  }


}
