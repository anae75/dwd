<? if(empty($users)) { ?>
  No users have joined the system.
<? } else { ?>
  <ul>
    <? foreach($users as $u ) { ?>
      <li> 
          <a href="javascript:void(0)" onclick="show_user_profile(<?=$u->user_id?>)">
            <?= MyUser::full_name($u) ?></a>
          </a>

      </li>
    <? } ?>
  </ul>
<? } ?>

<div id=context_help class=help>
  <span class="title">This is where you can see all the users in the system</span>
  <dl>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>
