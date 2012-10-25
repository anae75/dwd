<div class=submenubar>
  <ul>
    <li> <a href="/posts/create">Create a New Post</a> </li> 
  </ul>
</div>

<? if(empty($posts)) { ?>
  You haven't created any posts.  
  <br>
<? } ?>

<div class=stream >
  <span class=title> Your posts: </span>

  <? foreach($posts as $p ) { ?>
    <dt class="user_<?=$p->user_id ?>"> 
      <span class=user>
        <?= date('D M d, Y, h:ia', $p->created) ?>
      </span>
    </dt>
    <dd class="user_<?=$user->user_id ?>">
      <?= $p->text ?>
    </dd>
  <? } ?>

</div>

<div id=context_help class=help>
  <span class="title">This is where you can see all your posts</span>
  <dl>
  <dd> Your posts are displayed from newest to oldest. </dd>
  <dt> Haven't posted anything yet? </dt>
  <dd> Click "Create a New Post" to get started! </dd>
  </dl>

  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>
