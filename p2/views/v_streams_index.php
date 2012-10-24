
<div id=streams_container>

  <? foreach($streams as $stream) { ?>
    <div id=stream_<?=$stream->id?> class=stream >
      <span class=title> <?= $stream->name ?> </span>
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
  <? } ?>

</div>
