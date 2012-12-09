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

  public function done()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }
    $this->template->content = View::instance('v_stories_test');
    $this->template->title   = "Test";
    echo $this->template;
    echo "You're done!";
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
      echo "creating new story";
    }

    # unless hero? redirect_to character/new
    if(!$story->has_hero()) {
      Router::redirect("/stories/new_character");
    }

    # unless companions? redirect_to story/new_companions
    if(!$story->has_companions()) {
      Router::redirect("/stories/choose_companions");
    }

    # if(scenes.blank?) {
    #   session[:scenes] = story.select_scenes()        # story model takes care of current_scene
    # }

    if($story->finished()) {
      $story->finish();  # mark the story as completed
      Router::redirect("/stories/done");
    }

    # display the next scene and advance the scene pointer
    # render :play_scene
    $opts = Array();
    $opts["dont_advance"] = true;
    $scene = $story->pop($opts);

    # Setup view
    $this->template->content = View::instance('v_stories_next_scene');
    $this->template->title   = "The Story Continues";

    $this->template->set_global('story', $story);
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
      echo "creating new story";
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
        $img = $_POST['form_imagedata'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = "images/" . "foobar" . '.png';
        $success = file_put_contents($file, $data);
        echo $success;
  }

} # end class
