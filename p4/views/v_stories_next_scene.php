
  <div class=stage>
    <div class=canvas_holder width="600px" height="400px">
      <canvas id="hero_canvas" width="600px" height="400px"></canvas>
      <canvas id="obs_canvas" width="600px" height="400px"></canvas>
    </div>
    <span id="caption">caption</span>

    <div id=dialog_prompt class="dialog prompt">
      <textarea maxlength=100 rows=5 columns=100 name="dialog_text" id="dialog_text"> </textarea><br>
      <button type=button onclick="this.parentElement.firstElementChild.value='';">clear</button>
    </div>

    <div id=drawing_prompt class="prompt" style="position:absolute; width:300px; height:300px">
    <canvas id="drawing_canvas" width="300px" height="300px"></canvas>
    <button type=button id="clear_button" onclick="drawing.reset_canvas();">Clear</button>
    </div>
    
    <form id=dialog_form data-remote=true action="add_dialog" method="post" >
      <input type="hidden" id="shot_id" name="shot_id">
      <input type="hidden" id="character_id" name="character_id">
      <input type="hidden" id="prompt_imagedata" name="prompt_imagedata">
      <input type="hidden" id="prompt_text" name="prompt_text">
      <button type="button" onclick="submit_dialog_form()">Save your response.</button>
    </form>

  </div>

<? if(!isset($is_final_scene) || !$is_final_scene) { ?>
<a href="/stories/next_scene">Move to the next scene</a>
<? } ?>

<div id=context_help class=help>
  <span class="title">Viewing The Story</span>
  <dl>
  <dt> </dt>
  <dd> View the story as it unfolds.  From time to time you may be asked to contribute drawings or dialog using the
  canvas and the dialog bubble.  When you're done click the "Save your response" button.  When the scene is done you
  will be prompted to click a button to continue to the next scene.  </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>

<div id=scene_end_menu class=help>
  <? if(isset($is_final_scene) && $is_final_scene) { ?>
    Congratulations!  Click to <a href="/stories/welcome">Return Home</a>.
  <? } else { ?>
    Click to <a href="/stories/next_scene">Continue Your Adventure</a>.
  <? } ?>
</div>

<script>
  var shots = <?= json_encode($scene->export()) ?>;
  var drawing;
  $(document).ready(function() {
    drawing = new drawing_canvas("drawing_canvas");
    drawing.disable();
    $("#drawing_prompt").css("display", "none");
    play_shots(shots);
  });

  function submit_dialog_form() {
    // need some input
    if($("#dialog_prompt").is(":visible") && $("#dialog_text").val().length < 1) {
      alert("You have to say something.");
      return;
    }
    if($("#drawing_prompt").is(":visible") && drawing.is_empty()) {
      alert("You have to draw something.");
      return;
    }

    if($("#drawing_prompt").is(":visible")) {
      $("#prompt_imagedata").val($("#drawing_canvas")[0].toDataURL());
    } else {
      $("#prompt_imagedata").val(null);
    }
    $("#prompt_text").val($("#dialog_text").val());

    $.ajax({
      type: "POST",
      url: "/stories/add_dialog",
      data: { shot_id: $("#shot_id").val(), 
              prompt_text: $("#prompt_text").val(),
              prompt_imagedata: $("#prompt_imagedata").val()
              }
    }).success(function( msg ) {
      next_shot();
    });

  }

</script>



