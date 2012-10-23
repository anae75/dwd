<div class=submenubar>
  <ul>
    <li> <a href="/streams/create">Create a New Stream</a> </li> 
  </ul>
</div>

<div id=streams_container>

  <? foreach($streams as $stream) { ?>
    <div id=stream_<?=$stream->id?> stream_id=<?=$stream->id?> class=stream >
      <span class=title> <?= $stream->name ?> </span>
      <? if($stream->id != Stream::default_stream_id) {?>
        <button onclick="if(confirm('Are you sure you want to delete this stream?')) { window.location.href='/streams/delete/<?=$stream->id?>'}">Delete</button>
      <? } ?>
      <ul>
        
      <? foreach($stream->following as $f) { ?>
        <li class="user user_<?=$f->user_id ?>" user_id=<?=$f->user_id ?>> 
          <a href="javascript:void(0)" onclick="show_user_profile(<?=$f->user_id?>)">
            <?= $f->first_name ?> <?= $f->last_name ?>
          </a>
        </li>
      <? } ?>

      </ul>
    </div>
  <? } ?>

</div>

<script>
  function move_to_stream(user, new_stream)
  {
    // change the stream
    user_id = user.attr("user_id");
    new_stream_id = new_stream.attr("stream_id");
    $.ajax({
      type: "get",
      url: "/streams/move/" + user_id + "/" + new_stream_id, 
      success: function(resp) {
        // move the html element
        list = new_stream.find("ul");
        foo = list;
        user.appendTo(list);
      },
      error: function() { 
        alert("An error occurred while trying to move this user to a different stream."); 
      }
    });
  }

  function init()
  {
    $(".user").draggable({helper: "clone"});
    $(".stream").droppable({
      accept: ".user",
      drop: function(event, ui) {
          move_to_stream(ui.draggable, $(this));
        }
    });
  }

  $(document).ready(function()
  {
    init();
  });

</script>
