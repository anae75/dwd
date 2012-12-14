<?php
class stories_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
  } 

  public function test()
  {

    $this->template->content = View::instance('v_stories_test');
    $this->template->title   = "Test";
    echo $this->template;

    #var_dump($this->user);

    #$story = Story::current_story_for($this->user);
    #var_dump($story);
    $story = Story::create_for($this->user->user_id);
    var_dump($story);

    #$opts = Array();
    #$opts["user_id"] = $this->user->user_id;
    #$opts["use_external_content"] = true;
    #$scenes = Story::get_scenes($opts);
    #var_dump($scenes);


  }

  public function welcome()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # Setup view
    $this->template->content = View::instance('v_stories_welcome');
    $this->template->title   = "Your Mission";

    $story = Story::current_story_for($this->user);
    $this->template->set_global('story', $story);
            
    # Render template
    echo $this->template;

  }

  public function done()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    $scene = Story::get_epilogue_scene();

    # Setup view
    $this->template->content = View::instance('v_stories_next_scene');
    $this->template->title   = "The End";

    $this->template->set_global('scene', $scene);
            
    # Render template
    echo $this->template;
  }

  public function next_scene()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # retrieve the currently playing story or create a new one
    $story = Story::current_story_for($this->user);
    if(!$story) {
      $story = Story::create_for($this->user->user_id);
    }

    # unless hero? redirect_to character/new
    if(!$story->has_hero()) {
      Router::redirect("/stories/new_character");
    }

    # unless companions? redirect_to story/new_companions
    if(!$story->has_companions()) {
      Router::redirect("/stories/choose_companions");
    }

    if($story->finished()) {
      $story->finish();  # mark the story as completed
      Router::redirect("/stories/done");
    }

    # display the next scene and advance the scene pointer
    # render :play_scene
    $opts = Array();
    #$opts["dont_advance"] = true;  # TODO remove this in final version
    $scene = $story->pop($opts);

    # Setup view
    $this->template->content = View::instance('v_stories_next_scene');
    $this->template->title   = "The Story Continues";

    $this->template->set_global('story', $story);
    $this->template->set_global('scene', $scene);
            
    # Render template
    echo $this->template;

  }

  # create a new story and play the introduction
  public function new_story()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    Story::kill_unfinished_stories($this->user);
    $story = Story::create_for($this->user->user_id);
    $scene = Story::get_intro_scene();

    # Setup view
    $this->template->content = View::instance('v_stories_next_scene');
    $this->template->title   = "The Story Begins";

    $this->template->set_global('scene', $scene);
            
    # Render template
    echo $this->template;
  }

  public function choose_companions() 
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # retrieve the currently playing story or create a new one
    $story = Story::current_story_for($this->user);
    if(!$story) {
      $story = Story::create_for($this->user->user_id);
    }

    $opts["use_external_content"] = $this->user->use_external_content;
    $opts["user_id"] = $this->user->user_id;
    $opts["hero_id"] = $story->hero_id();
    $characters = Story::possible_companions($opts);

    # Setup view
    $this->template->content = View::instance('v_stories_choose_companions');
    $this->template->title   = "Choose Your Companions";

    $this->template->set_global('story', $story);
    $this->template->set_global('characters', $characters);

    # Render template
    echo $this->template;
  }

  public function select_companions()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # retrieve the currently playing story or create a new one
    $story = Story::current_story_for($this->user);
    if(!$story) {
      $story = Story::create_for($this->user->user_id);
    }

    $ids = Array();
    for($i = 1; $i <= 3; $i++) {
      $ids[] = $_POST["companion_" . $i . "_id"];
    }
    $story->set_companions($ids);

    Router::redirect("/stories/next_scene");
  }

  public function new_character() 
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # Setup view
    $this->template->content = View::instance('v_stories_new_character');
    $this->template->title   = "Create Your Character";
    echo $this->template;
  }

  public function p_new_character()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }
    # TODO error checking!

    # retrieve the currently playing story or create a new one
    $story = Story::current_story_for($this->user);
    if(!$story) {
      $story = Story::create_for($this->user->user_id);
    }

    # create a new character
    $opts = Array();
    $opts["user_id"] = $this->user->user_id;
    $opts["name"] = $_POST["name"];
    $opts["description"] = $_POST["description"];
    $opts["npc"] = 0;
    $char_id = Character::create($opts);

    # add image for the character
    $opts = Array();
    $opts["user_id"] = $this->user->user_id;
    $opts["character_id"] = $char_id;
    $img = $_POST['form_imagedata'];
    $img_id = UploadedImage::create($img, $opts);

    $story->set_hero($char_id);

    Router::redirect("/stories/next_scene");
  }

  # XXX AJAX
  public function add_dialog()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # retrieve the currently playing story or error out
    $story = Story::current_story_for($this->user);
    if(!$story) {
      Helper::send_error();
    }

    $story->add_response($_POST["shot_id"], $_POST["prompt_text"], $_POST["prompt_imagedata"]);

    echo "success";
  }

} # end class
