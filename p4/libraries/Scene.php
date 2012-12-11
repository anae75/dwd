<?php

class Scene {

  protected $_story;
  protected $_scene;
  protected $_shots;

  public function __construct($data, $story) 
  {
    $this->_scene = $data;
    $this->_story = $story;

    if($this->_scene->id) {
      $this->load_shots();
    }
  } 

  public function load_shots()
  {
    $sql = sprintf("select * from shots where scene_id=%d order by seq asc", $this->_scene->id);
    $this->_shots = DB::instance(DB_NAME)->select_rows($sql, "object");
    foreach($this->_shots as $shot) {
      $sql = sprintf("select positions.*, images.character_id, images.filename from positions LEFT OUTER JOIN images ON images.id = positions.image_id where shot_id=%d", $shot->id);
      $shot->positions = DB::instance(DB_NAME)->select_rows($sql, "object");
    }
  }

  ############################################################
  # Accessors
  ############################################################

  public function title()
  {
    return $this->_scene->title;
  }

  public function shots() 
  {
    return $this->_shots;
  }

  public function export()
  {
    $data = Array();
    foreach($this->_shots as $shot) {
      $data[] = $this->export_shot($shot);
    }
    return $data; 
  }

  public function export_shot($shot)
  {
    $hero_id = $this->_story->hero_id();
    $hero_image = $this->_story->hero_image();
   
    $data = Array();
    $data["shot_id"] = $shot->id;
    $data["caption"] = $shot->caption;
    $data["images"] = Array();
    $data["dialogs"] = Array();

    foreach($shot->positions as $pos) {

      switch($pos->type) { 
        case "npc":
          $img_url = "/".$pos->filename;
          $character_id = $pos->character_id;
          break;
        case "hero":
          $img_url = "/".$hero_image->filename;
          $character_id = $hero_id;
          break;
      }

      $img = Array();
      $img["posx"] = $pos->posx;
      $img["posy"] = $pos->posy;
      $img["scale"] = $pos->scale;
      $img["image_url"] = $img_url;
      $data["images"][$character_id] = $img;

      if($pos->dialog) {
        $data["dialogs"][$character_id] = $pos->dialog;
      }

      if($pos->prompt_dialog == 1) {
        $data["prompt_dialog"] = $character_id; 
      }
      if($pos->prompt_drawing == 1) {
        $data["prompt_drawing"] = $character_id; 
      }

    }
    return $data;
  }

} # end class
