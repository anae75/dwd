
<div id=streams_container>

  <? foreach($streams as $stream) { ?>
    <div id=stream_<?=$stream->id?> class=stream >
      <div class=title> <?= $stream->name ?> </div>
      <div class=stream_content>
        <dl>
        <? foreach($stream->posts() as $p) { ?>
          <dt class="user_<?=$p->user_id ?>"> 
            <span class=user>
              <a href="javascript:void(0)" onclick="show_user_profile(<?=$p->user_id?>)"> <?= $p->first_name ?></a>
            </span>
            at <?= date('D M d, Y, h:ia', $p->created) ?>
          </dt>
          <dd class="user_<?=$p->user_id ?>">
            <?= $p->text ?>
          </dd>
        <? } ?>
        </dl>
      </div>

    </div>
  <? } ?>

</div>

<div id=context_help class=help>
  <span class="title">This is your home page, where you can get the latest news from everyone you're following.</span>
  <dl>
  <dt>Empty stream?</dt>
  <dd>Don't worry --just click on "Browse Users" in the top menu bar and start following some users.</dd>
  <dt>Too many people to keep up with in one stream?</dt>
  <dd>Click "Manage Streams" in the top menu bar and create new streams for each group of people you're following.</dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>
