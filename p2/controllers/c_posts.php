<?php
class posts_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
  } 

  public function create()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # render
    $this->template->content = View::instance('v_posts_create');
    $this->template->title   = "Create a New Post";
    echo $this->template;

  }

  public function p_create() 
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }
    Helper::csrf_protect($_POST);

    $_POST['user_id']  = $this->user->user_id;
    $_POST['created']  = Time::now();
    $_POST['modified'] = Time::now();

    $user_id = DB::instance(DB_NAME)->insert("posts", $_POST);

    Router::redirect("/posts");
  }

  public function index() 
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # view setup
    $this->template->content = View::instance('v_posts_index');
    $this->template->title   = "Posts";

    $posts = $this->userObj->posts();
    $this->template->set_global('posts', $posts);

    # render
    echo $this->template;
  }

} # end class
