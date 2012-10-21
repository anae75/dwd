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
  <? if(array_key_exists($profiled_user->user_id, $user->following)) { ?>
    You are already following this user. 
  <? } else { ?>
    You are not following this user. 
    <button onclick="follow(<?= $profiled_user->user_id ?>);">follow</button>
  <? } ?>
<? } ?>
