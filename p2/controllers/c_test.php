<?php
class test_controller extends base_controller {

  # set a cookie
  public function cookie() {
      $attr =  "foo";
      $olddata = @$_COOKIE[$attr];
      $data = Time::now();
      @setcookie($attr, $data, strtotime('+1 year'), '/');
      echo "old cookie " . $attr . "= " . $olddata . "<br>"; 
      echo "set cookie " . $attr . "= " . $data . "<br>"; 
  }

  public function echo_input($data=null) 
  {
    echo $data; 
  }

  public function index()
  {
    # Setup view
    $this->template->content = View::instance('v_test_index');
    $this->template->title   = "Misc Test Stuff";

    # Render template
    echo $this->template;

  }

} # end class
