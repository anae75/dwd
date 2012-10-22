Streams go here.

<div id=streams_container>

  <? foreach($streams as $stream) { ?>
    <div id=stream_<?=$stream->id?> class=stream >
      <span class=title> <?= $stream->name ?> </span>
      <dl>
        
      <? foreach($stream->posts() as $p) { ?>
        <dt> 
          <span class=user><?= $p->first_name ?></span>
          at <?= date('D M d, Y, h:ia', $p->created) ?>
        </dt>
        <dd>
          <?= $p->text ?>
        </dd>
      <? } ?>

      </dl>
    </div>
  <? } ?>

</div>
