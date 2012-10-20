<?= $user->first_name?>'s posts go here.

<? if(empty($posts)) { ?>
  You haven't created any posts.  
<? } ?>

<ul>
  <? foreach($posts as $post ) { ?>
    <li> <?= date('D M d, Y, h:ia', $post->created) ?>: <?= $post->text ?> </li>
  <? } ?>
</ul>
