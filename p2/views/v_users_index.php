<? if(empty($users)) { ?>
  No users have joined the system.
<? } else { ?>
  <ul>
    <? foreach($users as $u ) { ?>
      <li> 
          <a href="/users/profile/<?= $u->user_id ?>"><?= MyUser::full_name($u) ?></a>
          <? if(array_key_exists($u->user_id, $user->following)) { ?>
            (You are following this user.)
          <? } ?>
      </li>
    <? } ?>
  </ul>
<? } ?>

