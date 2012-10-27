<?php
class streams_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
  } 

  public function index()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
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

  public function manage()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }
    # render
    $this->template->content = View::instance('v_streams_manage');
    $this->template->title   = "Manage Streams";
    $streams = $this->userObj->streams();
    $this->template->set_global('streams', $streams);
    echo $this->template;
  }

  public function create() 
  {
    if(!$this->user) {
      Flash::set("Please log in.");
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
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }
    Helper::csrf_protect($_POST);
    $_POST['user_id']  = $this->user->user_id;
    $user_id = DB::instance(DB_NAME)->insert("streams", $_POST);
    Router::redirect("/streams/manage");
  }

  public function move($user_id, $stream_id) 
  {
    if(!$this->user) {
      Helper::send_error();
      return;
    }
    if(Stream::move_stream($this->user->user_id, $user_id, $stream_id)) {
      echo "ok";
    } else {
      Helper::send_error();
    }
  }

  public function delete($stream_id)
  {
    if(!$this->user) {
      Helper::send_error();
      return;
    }
    if(Stream::delete_stream($this->user->user_id, $stream_id)) {
      Router::redirect("/streams/manage");
    } else {
      Helper::send_error();
    }
  }

};
