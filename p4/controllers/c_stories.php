<?php
class stories_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
  } 

  public function test()
  {
    echo "This is a test";
    #var_dump($this->user);

    #$story = Story::current_story_for($this->user);
    #var_dump($story);
    #$story = Story::create_for($this->user->user_id);
    #var_dump($story);

    $opts = Array();
    $opts["user_id"] = $this->user->user_id;
    $opts["use_external_content"] = true;
    $scenes = Story::get_scenes($opts);
    var_dump($scenes);

  }

  public function next_scene()
  {
    # retrieve the currently playing story or create a new one
    $story = Story::current_story_for($this->user);
    if(!$story) {
      $story = Story::create_for($this->user->user_id);
      $story->initialize();
    }
    var_dump($story);

    # unless hero? redirect_to character/new
    # unless companions? redirect_to story/new_companions
    # if(scenes.blank?) {
    #   session[:scenes] = story.select_scenes()        # story model takes care of current_scene
    # }
    # if(story.finished?)
    #   redirect_to story/new
    # end
    # load_scene(story.next_scene)
    # render :play_scene

  }

} # end class
