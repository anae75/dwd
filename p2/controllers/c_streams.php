<?php
class streams_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
  } 

  # 
  # Display all streams of user
  # side effects: none
  # 
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

  # 
  # Display management view for all streams of user
  # side effects: none
  # 
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

  # 
  # Display create posts screen.
  # side effects: none
  # 
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

  #
  # Create a stream
  # XXX side effects: yes (relying on DB::insert to sanitize input)
  #
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

  #
  # Move user from one stream to another
  # XXX side effects: yes
  #
  public function move($user_id, $stream_id) 
  {
    if(!$this->user) {
      Helper::send_error();
      return;
    }
    # MUST be ajax because relying on Helper::csrf_protect_ajax
    if(!Utils::is_ajax()) {
      Helper::send_error();
      return;
    }
    $user_id = DB::instance(DB_NAME)->sanitize($user_id);
    $stream_id = DB::instance(DB_NAME)->sanitize($stream_id);
    if(Stream::move_stream($this->user->user_id, $user_id, $stream_id)) {
      echo "ok";
    } else {
      Helper::send_error();
    }
  }

  #
  # Delete a stream owned by current user
  # XXX side effects: yes
  #
  public function delete($stream_id)
  {
    if(!$this->user) {
      Helper::send_error();
      return;
    }
    # MUST be ajax because relying on Helper::csrf_protect_ajax
    if(!Utils::is_ajax()) {
      Helper::send_error();
      return;
    }
    $stream_id = DB::instance(DB_NAME)->sanitize($stream_id);
    if(Stream::delete_stream($this->user->user_id, $stream_id)) {
      echo "success";
    } else {
      Helper::send_error();
    }
  }

};
