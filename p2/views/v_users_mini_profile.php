<div id="mymodal">

  <a href="/users/profile/<?=$profiled_user->user_id?>"><?= MyUser::full_name($profiled_user) ?></a>
  <br>

  <? $p = $profiled_user->most_recent_post; ?>
  <dl>
    <dt > 
      Most recent post at <?= date('D M d, Y, h:ia', $p->created) ?>
    </dt>
    <dd >
      <?= $p->text ?>
    </dd>

    <dt>
      This user has <?= $profiled_user->nfollowers ?> followers.
    </dt>

    <dt>
      <a href="/users/profile/<?=$profiled_user->user_id?>">Full Profile</a>
    </dt>

  </dl>


  <? $puid = $profiled_user->user_id ?>
  <? if(array_key_exists($puid, $user->following)) { ?>
    <span id="label_unfollow_<?=$puid?>">You are already following this user.</a></span> 
    <button id="button_unfollow_<?=$puid?>" onclick="unfollow(this, <?= $puid ?>);">unfollow</button>
  <? } else { ?>
    <span id="label_follow_<?=$puid?>">You are not following this user.</a></span> 
    <button id="button_follow_<?=$puid?>" onclick="follow(this, <?= $puid ?>);">follow</button>
  <? } ?>

</div>
