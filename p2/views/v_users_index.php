<? if(empty($users)) { ?>
  No users have joined the system.
<? } else { ?>
  <ul>
    <? foreach($users as $u ) { ?>
      <li> 
          <a href="javascript:void(0)" onclick="show_user_profile(<?=$u->user_id?>)">
            <?= MyUser::full_name($u) ?></a>
          </a>

          <? if(array_key_exists($u->user_id, $user->following)) { $display = "inline"; } else { $display="none"; } ?>
          <span class="following_<?=$u->user_id?>" style="display: <?=$display?>">(You are following this user.)</span>
      </li>
    <? } ?>
  </ul>
<? } ?>

<div id=context_help class=help>
  <span class="title">This is where you can see all the users in the system</span>
  <dl>
  <dt> Browse </dt>
  <dd> Click on the user name to see some quick info on the user including their latest post and how many followers they
  have.  You can follow or unfollow them immediately or click on "Full Profile" to get more information. </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>
