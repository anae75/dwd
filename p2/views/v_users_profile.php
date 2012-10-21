<h1>This is the profile of <?=$profiled_user->first_name?></h1>

<h2>Following:</h2>
<ul>
  <? foreach($following as $u ) { ?>
    <li> <a href="/users/profile/<?= $u->user_id ?>"><?= MyUser::full_name($u) ?></a> </li>
  <? } ?>
</ul>

<h2>Followed By:</h2>
<ul>
  <? foreach($followers as $u ) { ?>
    <li> <a href="/users/profile/<?= $u->user_id ?>"><?= MyUser::full_name($u) ?></a> </li>
  <? } ?>
</ul>

<? if(!$viewing_self) { ?>
  <? $puid = $profiled_user->user_id ?>
  <? if(array_key_exists($puid, $user->following)) { ?>
    <span id="label_unfollow_<?=$puid?>">You are already following this user.</a></span> 
    <button id="button_unfollow_<?=$puid?>" onclick="unfollow(this, <?= $puid ?>);">unfollow</button>
  <? } else { ?>
    <span id="label_follow_<?=$puid?>">You are not following this user.</a></span> 
    <button id="button_follow_<?=$puid?>" onclick="follow(this, <?= $puid ?>);">follow</button>
  <? } ?>
<? } ?>
