<?php
class streams_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
  } 

  public function index()
  {
    if(!$this->user) {
      Router::redirect("/users/login");
      return;
    }
    # render
    $this->template->content = View::instance('v_streams_index');
    $this->template->title   = "Your Live Streams";
    $streams = $this->userObj->streams();
    $this->template->set_global('streams', $streams);
    echo $this->template;
  }

  public function create() 
  {
    if(!$this->user) {
      Router::redirect("/users/login");
      return;
    }
    # render
    $this->template->content = View::instance('v_streams_create');
    $this->template->title   = "Create a New Stream";
    echo $this->template;
  }

  public function p_create() 
  {
    if(!$this->user) {
      Router::redirect("/users/login");
      return;
    }
    $_POST['user_id']  = $this->user->user_id;
    $user_id = DB::instance(DB_NAME)->insert("streams", $_POST);
    Router::redirect("/streams");
  }

};
