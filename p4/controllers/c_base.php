<?php

class base_controller {

  public $user;
  public $userObj;
  public $template;
  public $email_template;
  public $client_files;

  /*-------------------------------------------------------------------------------------------------
  -------------------------------------------------------------------------------------------------*/
  public function __construct() {

    Flash::init();

    # Instantiate User class
    $this->userObj = new MyUser();

    # Authenticate / load user
    $this->user = $this->userObj->authenticate();			

    # Set up templates
    $this->template 	  = View::instance('_v_template');
    $this->email_template = View::instance('_v_email');			

    # So we can use $user in views			
    $this->template->set_global('user', $this->user);
	    
    # default client files
    $this->client_files = Array(
      "/css/p2.css",
      "/javascripts/jquery.validate.min.js",
      "/javascripts/story.js",
      "/javascripts/p2.js"
    );
    $this->template->client_files = Utils::load_client_files($this->client_files); 

    if(Utils::is_ajax()) {
      Helper::csrf_protect_ajax();
    }

  }

} # eoc
