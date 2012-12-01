<? if($viewing_self) { ?>
<div class=submenubar>
  <ul>
    <li> <a href="/users/edit">Edit Your Settings</a> </li> 
  </ul>
</div>
<? } ?>

<h1>This is the profile of <?= MyUser::full_name($profiled_user) ?>
<? if($viewing_self) { ?> <span class="notice">(That's you!)</span> <? } ?>
</h1>

<? if($viewing_self) { ?>
  <h3>Your story settings</h3>
  Share your content with other users: <?= $user->publish_content ? "yes" : "no" ?><br>
  Incorporate content from other users: <?= $user->use_external_content ? "yes" : "no"  ?><br>
<? } ?> 

<br>

<? if(!$viewing_self) { ?>
  <? $puid = $profiled_user->user_id ?>
<? } ?>

<div id=context_help class=help>
  <span class="title">This is where you can get more information on a user</span>
  <dl>
  <dt> Profile </dt>
  <dd>  </dd>
  <dt> Viewing your own profile? Need to change some settings?</dt>
  <dd> Click "Edit Your Settings" to change your own settings such as your email or password. </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>
