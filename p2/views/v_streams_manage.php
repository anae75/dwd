<div class=submenubar>
  <ul>
    <li> <a href="/streams/create">Create a New Stream</a> </li> 
  </ul>
</div>

<div id=streams_container>

  <? foreach($streams as $stream) { ?>
    <div id=stream_<?=$stream->id?> data-streamid=<?=$stream->id?> class=stream >
      <div class=title title="<?= htmlspecialchars($stream->description) ?>"  > <?= htmlspecialchars($stream->name) ?> 

      <? if($stream->id != Stream::default_stream_id) {?>
        <button onclick="if(confirm('Are you sure you want to delete this stream?')) { $.ajax({
                              url: '/streams/delete/<?=$stream->id?>',
                              type: 'post',
                              success: function() { window.location.href='/streams/manage'; },
                              error: function () { alert('An error occurred while trying to delete this stream.'); }
                              })}">Delete</button>
        <br>
      <? } ?>
      </div >

      <div class=stream_content>
        <? if(!$stream->following) { ?>
          <div class="ifempty">This stream is empty.  Follow some users and drag them to this stream.</div>
        <? } ?>

        <ul>
        <? foreach($stream->following as $f) { ?>
          <li class="user user_<?=$f->user_id ?>" data-userid=<?=$f->user_id ?>> 
            <a href="javascript:void(0)" onclick="show_user_profile(<?=$f->user_id?>)">
              <?= MyUser::full_name($f) ?>
            </a>
          </li>
        <? } ?>
        </ul>

      </div>

    </div>
  <? } ?>

</div>

<div id=context_help class=help>
  <span class="title">This is where you can manage all your streams.</span>
  <dl>
  <dt>Getting Started</dt>
  <dd>
      By default, everyone you follow starts off in your main stream all the way at the left.
      Drag and drop user names to move them from one stream to another.</dd>
  <dt>Creating Streams</dt>
  <dd>Click "Create a New Stream" and enter the name of your stream and an optional description.  
      For example, you might want a stream for Family and another one for Work or Friends.
  </dd>
  <dt>Deleting streams</dt>
  <dd>Click the "delete" button at the top of each stream.  Don't worry, all the users you've placed in that stream will
  be moved back into the main stream --you won't lose them!</dd>
  <dd>You cannot delete the main stream.</dd>
  </dl>
</div>

<script>
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
