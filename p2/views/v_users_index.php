<? if(empty($users)) { ?>
  No users have joined the system.
<? } else { ?>
  <ul>
    <? foreach($users as $u ) { ?>
      <li> 
          <a href="/users/profile/<?= $u->user_id ?>"><?= MyUser::full_name($u) ?></a>
          <button onclick="follow(<?= $u->user_id ?>);">follow</button>
      </li>
    <? } ?>
  </ul>
<? } ?>
