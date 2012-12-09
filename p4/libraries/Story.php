<?php

class Story {

  protected $_story;
  protected $_scenes;

  public function __construct($data) 
  {
    # parent::__construct();
    $this->_story = $data;
    # load scenes
    if($this->_story->id) {
      $this->load_scenes();
    }
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
    DB::instance(DB_NAME)->update("stories", $data, "WHERE id = " . $this->_story->id);
    $this->_story->hero_id = $hero_id;
  }

  public function n_scenes()
  {
    return sizeof($this->_scenes);
  }

  ############################################################
  # Story setup methods
  ############################################################

  public function load_scenes()
  {
    $sql = sprintf("select * from scenes inner join story_scenes on scenes.id=story_scenes.scene_id where story_id=%d order by seq asc", $this->_story->id);
    $rows = DB::instance(DB_NAME)->select_rows($sql, "object");
    if(!$rows) {
      return;
    }
    $this->_scenes = Array();
    foreach($rows as $row) {
      $scene = new Scene($row);
      $this->_scenes[] = $scene;
    }
  }

  public function pop($opts)
  {
    if($this->finished()) {
      return null;
    }
    $scene = $this->_scenes[$this->_story->current_scene];

    # advance the scene pointer
    if(!$opts["dont_advance"]) {
      $this->_story->current_scene += 1;
      $data = Array();
      $data["current_scene"] = $this->_story->current_scene;
      DB::instance(DB_NAME)->update("stories", $data, "WHERE id = " . $this->_story->id);
    }

    return $scene;
  }

  public function finished()
  {
    return $this->_story->completed == 1 || ($this->_story->current_scene >= sizeof($this->_scenes));
  }

  public function finish()
  {
    $data = Array();
    $data["completed"] = 1;
    DB::instance(DB_NAME)->update("stories", $data, "WHERE id = " . $this->_story->id);
    $this->_story->completed = 1;
  }

  public function has_companions()
  {
    $n = 0;
    if($this->_story->companion_1_id) { $n++; }
    if($this->_story->companion_2_id) { $n++; }
    if($this->_story->companion_3_id) { $n++; }
    return ($n == 3);
  }

  public function set_companions($ids)
  {
    $data = Array();
    $data["companion_1_id"] = $this->_story->companion_1_id = $ids[0];
    $data["companion_2_id"] = $this->_story->companion_2_id = $ids[1];
    $data["companion_3_id"] = $this->_story->companion_3_id = $ids[2];
    DB::instance(DB_NAME)->update("stories", $data, "WHERE id = " . $this->_story->id);
  }

  public function hero_id()
  {
    return $this->_story->hero_id;
  }

  ############################################################
  # Statics
  ############################################################

  public static function create_for($user_id)
  {
    # create a new story
    $data = Array();
    $data['user_id'] = $user_id;
    $story_id = DB::instance(DB_NAME)->insert("stories", $data);

    # choose scenes
    $opts = Array();
    $opts["user_id"] = $user_id;
    $opts["use_external_content"] = true;
    $scenes = Story::get_scenes($opts);

    # insert them into the story
    $seq = 0;
    foreach($scenes as $scene) {
      $data = Array();
      $data['story_id'] = $story_id;
      $data['scene_id'] = $scene->id;
      $data['seq'] = $seq;
      $seq++;
      $id = DB::instance(DB_NAME)->insert("story_scenes", $data);
    }

    # initialize a new story object 
    $sql = sprintf("select * from stories where id=%d", $story_id); 
    $data = DB::instance(DB_NAME)->select_row($sql, "object");
    $story = new Story($data);

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
    $sql .= sprintf(" where users.publish_content=1 ");
    if(!$opts["use_external_content"]) {
      # use only default content or the user's own content
      $sql .= sprintf(" and (users.user_id=%d or users.superuser=1) ", $opts["user_id"]);
    }
    if(!isset($opts["max"])) {
      $opts["max"] = 3;
    }
    $sql .= sprintf(" order by rand() limit %d ", $opts["max"]); 
    $data = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $data;
  }

  public static function possible_companions($opts) 
  {
    $sql = sprintf("select characters.*, images.filename from characters inner join users on characters.user_id=users.user_id and npc=0 inner join images on images.character_id=characters.id");
    if(!$opts["use_external_content"]) {
      # use only default content or the user's own content
      $sql .= sprintf(" and (users.user_id=%d or users.superuser=1) ", $opts["user_id"]);
    }
    $sql .= sprintf(" and users.publish_content=1 ");
    if($opts["hero_id"]) {
      $sql .= sprintf(" and not characters.id=%d ", $opts["hero_id"]);
    }
    $data = DB::instance(DB_NAME)->select_rows($sql, "object");
    return $data;
  }

} # end class
