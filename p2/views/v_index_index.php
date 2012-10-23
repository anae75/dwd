
<div class=centering>

<div id=welcome title="Welcome to MMMMicroblogger">
Welcome to <br><span class=name>MMMMicroblogger</span>
</div>

<div id=recent_posts class=stream>
<h2>Here's what people are saying:</h2>
<dl>
<? foreach($posts as $p) { ?>
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
