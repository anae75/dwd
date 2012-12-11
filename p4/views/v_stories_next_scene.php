
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
    
    <form id=dialog_form data-remote=true action="add_dialog" method="post">
      <input type="hidden" id="shot_id" name="shot_id">
      <input type="hidden" id="character_id" name="character_id">
      <input type="hidden" id="prompt_imagedata" name="prompt_imagedata">
      <input type="hidden" id="prompt_text" name="prompt_text">
      <button type="button" onclick="submit_dialog_form()">submit this puppy</button>
    </form>

  </div>

The story has <?= $story->n_scenes() ?> scenes. <br>

<h2> Current Scene: <?= $scene->title() ?> </h2>
<?= var_dump($scene); ?> <br>
<h3> shots </h3>
<?= print_r($scene->export() ); ?> <br>

<h2> Current Story </h2>
<?= var_dump($story); ?> <br>

<a href="/stories/next_scene">Move to the next scene</a>

<div id=context_help class=help>
  <span class="title">The Story Continues</span>
  <dl>
  <dt> The Story </dt>
  <dd> </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
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
    $("#dialog_form").submit();
  }

</script>



