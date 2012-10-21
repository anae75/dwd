<? if(empty($users)) { ?>
  No users have joined the system.
<? } else { ?>
  <ul>
    <? foreach($users as $u ) { ?>
      <li> <?= sprintf("%s %s", $u->first_name, $u->last_name) ?> 
          <button onclick="follow(<?= $u->user_id ?>);">follow</button>
      </li>
    <? } ?>
  </ul>
<? } ?>
