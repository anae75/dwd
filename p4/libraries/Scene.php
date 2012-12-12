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

  protected $_sayings = Array("Nice Try.", "That didn't work.", "Sorry, no.", "Nope.", "Better luck next time.", "Ummmm... no.");
  public function random_saying() 
  {
    return $this->_sayings[rand(0, count($this->_sayings)-1)];
  }

  public function export()
  {
    $data = Array();
    foreach($this->_shots as $shot) {
      $responses = $this->get_responses($shot);
      foreach($responses as $r) {
        $data[] = $this->export_shot($shot, $r);
        $data[] = $this->text_frame($this->random_saying());
      }
      $data[] = $this->export_shot($shot, null);
    }
    return $data; 
  }

  public function get_responses($shot) {
    $companion_ids = join(",", $this->_story->companion_ids());
    $sql = sprintf("select * from responses where shot_id=%d and character_id in (%s)", $shot->id, $companion_ids);
    $responses = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $responses;
  }

  public function text_frame($text)
  {
    $data = Array();
    $data["text"] = $text;
    return $data;
  }

  public function export_shot($shot, $response)
  {
    $hero_id = $this->_story->hero_id();
    $hero_image = $this->_story->hero_image();

    $data = Array();
    $data["shot_id"] = $shot->id;
    $data["caption"] = $shot->caption;
    $data["text"] = $shot->text;

    # avoid emitting empty arrays because the scene playing machinery gets confused
    if($shot->positions) {
      $data["images"] = Array();
      $data["dialogs"] = Array();
    }

    foreach($shot->positions as $pos) {

      # if this position contains the hero, substitute in the hero of the current story
      # if this is a response, substitute in the companion character
      switch($pos->type) { 
        case "npc":
          $img_url = "/".$pos->filename;
          $character_id = $pos->character_id;
          break;
        case "hero":
          if($response) {
            $character_id = (int) $response->character_id;
            $img_url = "/".$this->_story->companion($character_id)->filename; # XXX
          } else {
            $img_url = "/".$hero_image->filename;
            $character_id = $hero_id;
          }
          break;
      }

      $img = Array();
      $img["posx"] = (int) $pos->posx;
      $img["posy"] = (int) $pos->posy;
      $img["scale"] = (int) $pos->scale;
      $img["image_url"] = $img_url;
      $data["images"][$character_id] = $img;

      # add response image
      if($pos->type == "hero" && $response && $response->image_filename) {
        $img = Array();
        $img["posx"] = (int) $pos->posx - 150;
        $img["posy"] = (int) $pos->posy;
        $img["scale"] = (int) 1;
        $img["image_url"] = "/" . $response->image_filename;
        $data["images"]["response"] = $img;
      }

      if($pos->dialog) {
        $data["dialogs"][$character_id] = $pos->dialog;
      }
      if($response) {
        $data["dialogs"][$character_id] = $response->text;
      }

      if(!$response) {
        if($pos->prompt_dialog == 1) {
          $data["prompt_dialog"] = $character_id; 
        }
        if($pos->prompt_drawing == 1) {
          $data["prompt_drawing"] = $character_id; 
        }
      }

    }

    return $data;
  }

} # end class
