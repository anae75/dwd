<h1> Draw your character </h1>
Here are a couple of templates to help you out

<div id = "#content_container">

    <div class=canvas_holder width="300px" height="300px">
      <canvas id="drawing_canvas" width="300px" height="300px"></canvas>
    </div>

  <form id=character_form method=post action="/stories/p_new_character">
    <input type=hidden id=form_imagedata name=form_imagedata>
    <ul>
    <li><label>Name:</label> <input type=text name=name></li>
    <li><label>Description:</label><input type=text name=description></li>
    </ul>
    <button type=button id="clear_button" onclick="reset_canvas('drawing_canvas');">Clear</button>
    <button type=button onclick="submit_form();">Submit </button>
  </form>

  <div id="templates">
  <h2> Choose A Template </h2>
    <ul class="imagelist">
    </ul>
  </div>

</div>

<script>
  var templates = ["/images/ctemplate_01.png", "/images/ctemplate_02.png"];
  var template_images = Array();
  var drawing;

  $(document).ready(function() {
    drawing = new drawing_canvas("drawing_canvas");
    load_templates();
    // point out required fields
  });

  function submit_form()
  {
    $("#form_imagedata").val($("#drawing_canvas")[0].toDataURL());
    $("#character_form").submit();
  }

  function load_templates()
  {
    if(templates) {
      var sem = new simple_sem(templates.length, function(){ finish_setup(); });
      for(var i = 0; i < templates.length; i++) {
        var image = new Image();
        var path = templates[i];
        template_images[path]=image;
        image.onload = function() {
          sem.p();
        }
        image.src = path; 
      }
    } else {
      finish_setup();
    }
  }

  function use_template(image)
  {
    drawing.reset_canvas();
    drawing.draw_image(image, 0, 0);
  }

  // separate function to get around @#@$% scoping
  function make_template_item(parent, path, image)
  {
      var obj = $("<li class='template'><img src='" + path + "'></li>").appendTo(parent);
      obj.click(function() { use_template(image); });
  }

  function display_templates()
  {
    var parent = $("#templates ul");
    for(var path in template_images) {
      make_template_item(parent, path, template_images[path]);
    }
  }

  // finish set up after all images are loaded
  function finish_setup()
  {
      display_templates()
      $(".template").first().click();
      drawing.enable();
  }

</script>


