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
    # unless companions? redirect_to story/new_companions
    # if(scenes.blank?) {
    #   session[:scenes] = story.select_scenes()        # story model takes care of current_scene
    # }

    if($story->finished()) {
      $story->finish();  # mark the story as completed
      Router::redirect("/stories/done");
    }

    # display the next scene and advance the scene pointer
    # render :play_scene
    $scene = $story->pop();

    # Setup view
    $this->template->content = View::instance('v_stories_next_scene');
    $this->template->title   = "The Story Continues";

    $users = MyUser::users();
    $this->template->set_global('story', $story);
    $this->template->set_global('scene', $scene);
            
    # Render template
    echo $this->template;

  }

} # end class
