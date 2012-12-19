
<div id="content_container" class="centering">

  <div id="left_sidebar">

    <div class=canvas_holder style="width:300px; height:300px">
        <canvas id="drawing_canvas" width="300px" height="300px"></canvas>
    </div>

    <form id=character_form method=post action="/stories/p_new_character">
      <input type=hidden id=form_imagedata name=form_imagedata>
      <ul>
      <li><label>Name:</label> <input type=text name=name></li>
      <li><label>Description:</label><input type=text name=description></li>
      </ul>
      <button type=button id="clear_button" onclick="drawing.reset_canvas();">Clear</button>
      <button type=button onclick="submit_form();">Create My Character</button>
    </form>
  </div>

  <div id="right_sidebar">
    <h1> Draw your character </h1>
    <div id="templates">
      Here are a couple of templates to help you out:
      <ul class="imagelist">
      </ul>
    </div>
  </div>

</div>

<div id=context_help class=help>
  <span class="title">Creating Your Hero</span>
  <dl>
  <dt> </dt>
  <dd> This is where you create your hero.  Draw in the canvas to customize your character.  Two templates are supplied to help you along (click to switch between them), or you can draw freehand by
  clicking the "clear" button.  </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
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
    if(drawing.is_empty()) {
      alert("You have to draw something.");
      return;
    }
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


