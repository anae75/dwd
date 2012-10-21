<? if(empty($users)) { ?>
  No users have joined the system.
<? } else { ?>
  <ul>
    <? foreach($users as $u ) { ?>
      <li> 
          <a href="/users/profile/<?= $u->user_id ?>"><?= MyUser::full_name($u) ?></a>
          <? if(!array_key_exists($u->user_id, $user->following)) { ?>
            <button id="button_follow_<?=$u->user_id?>" onclick="follow(this, <?= $u->user_id?>);">follow</button>
          <? } ?>
      </li>
    <? } ?>
  </ul>
<? } ?>
