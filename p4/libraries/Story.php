<?php

class Story {

  protected $_story;

  public function __construct($data) 
  {
    # parent::__construct();
    $this->_story = $data;
  } 

  ############################################################
  # Accessors
  ############################################################

  public function has_hero()
  {
    return isset($this->_story->hero_id);
  }

  public function set_hero($hero_id)
  {
    $data = Array();
    $data["hero_id"] = $hero_id;
    DB::instance(DB_NAME)->update("stories", $data, "WHERE story_id = " . $this->_story->id);
    $this->_story->hero_id = $hero_id;
  }

  ############################################################
  # Story setup methods
  ############################################################

  # initialize the story 
  public function initialize()
  {
    # choose scenes
    $opts = Array();
    $opts["user_id"] = $this->user->user_id;
    $opts["use_external_content"] = true;
    $scenes = Story::get_scenes($opts);

    # insert them into the story
    $seq = 0;
    foreach($scenes as $scene) {
      $data = Array();
      $data['story_id'] = $this->_story->id;
      $data['scene_id'] = $scene->id;
      $data['seq'] = $seq;
      $seq++;
      $id = DB::instance(DB_NAME)->insert("story_scenes", $data);
    }
  }

  ############################################################
  # Statics
  ############################################################

  public static function create_for($user_id)
  {
    $data = Array();
    $data['user_id'] = $user_id;
    $data['id'] = DB::instance(DB_NAME)->insert("stories", $data);
    $story = new Story((object) $data);
    return $story;
  }

  public static function current_story_for($user)
  {
    # find the newest uncompleted story
    $sql = sprintf("select * from stories where user_id=%d and completed=0 order by created_at desc limit 1", $user->user_id); 
    $data = DB::instance(DB_NAME)->select_row($sql, "object");
    if(!$data) {
      return null;
    }
    $story = new Story($data);
    return $story;
  }

  # get scenes based on options
  # user_id
  # use_external_content
  # max
  public static function get_scenes($opts)
  {
    $sql = sprintf("select scenes.* from scenes inner join users on scenes.user_id=users.user_id ");
    if($opts["use_external_content"]) {
    } else { 
      # use only default content or the user's own content
      $sql .= sprintf(" where users.user_id=%d or users.superuser=1 ", $opts["user_id"]);
    }
    if(!isset($opts["max"])) {
      $opts["max"] = 3;
    }
    $sql .= sprintf(" order by rand() limit %d ", $opts["max"]); 
    $data = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $data;
  }

} # end class
