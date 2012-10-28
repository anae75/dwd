<?php

class index_controller extends base_controller {

  public function __construct() {
    parent::__construct();
  } 

  /*-------------------------------------------------------------------------------------------------
  Access via http://yourapp.com/index/index/
  -------------------------------------------------------------------------------------------------*/

  #
  # This is the main "welcome" page for the site.
  #
  # side effects: none
  #
  public function index() {
    # logged in users go directly to their streams
    if($this->user) {
      Router::redirect("/streams");
      return;
    }

    # render
    $this->template->content = View::instance('v_index_index');
    $this->template->title = "Welcome";

    $posts = Post::recent();
    $this->template->set_global('posts', $posts);

    echo $this->template;

  }

} // end class
