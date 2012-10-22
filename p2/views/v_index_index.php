
<h2>Recent posts:</h2>
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

