<?php

class Scene {

  protected $_scene;
  protected $_shots;

  public function __construct($data) 
  {
    $this->_scene = $data;

    if($this->_scene->id) {
      $this->load_shots();
    }
  } 

  public function load_shots()
  {
  }

  ############################################################
  # Accessors
  ############################################################

  public function title()
  {
    return $this->_scene->title;
  }

} # end class
