<h1> You can't go into the tunnels alone!  Choose three companions to help you out. </h1>

<div>
<ul class="imagelist">
  <? foreach($characters as $char) { ?>
    <li > 
      <div class="character" data-id=<?= $char->id ?> id=char_<?= $char->id ?>>
      <img src="<?= "/" . $char->filename ?>"> <br>
      <?= $char->name ?>
      </div>
    </li>
  <? } ?>
</ul>
</div>

<br>
  <form id=companion_form method=post action="/stories/select_companions">
    <input type="hidden" id=companion_1_id name=companion_1_id />
    <input type="hidden" id=companion_2_id name=companion_2_id />
    <input type="hidden" id=companion_3_id name=companion_3_id />
    <button onclick="set_companions()">Continue the story.</button>
  </form>


<div id=context_help class=help>
  <span class="title">Choose your companions</span>
  <dl>
  <dt> The Story </dt>
  <dd> </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>

<script>
  $(document).ready( function() {
    $(".character").click(function() {
      choose_companion($(this));
    });
  });

  var max = 3;
  function choose_companion(elem)
  {
    if( elem.hasClass("selected") ) {
      elem.removeClass("selected");
    } else {
      nchosen = $(".selected").length;
      if(nchosen >= max) {
        alert("Please choose only " + max + " companions. When you've made your final selection continue the story.");
        return;
      }
      elem.addClass("selected");
    }
  }

  function set_companions()
  {
    chosen = $(".selected");
    if(chosen.length < max) {
      alert("Please choose " + max + " companions.");
      return;
    }
    $("#companion_1_id").val( chosen.eq(0).attr("data-id") );
    $("#companion_2_id").val( chosen.eq(1).attr("data-id") );
    $("#companion_3_id").val( chosen.eq(2).attr("data-id") );
  }
</script>

