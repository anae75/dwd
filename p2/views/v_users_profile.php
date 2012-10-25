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
  You have <?= $profiled_user->nfollowers ?> followers.
<? } else { ?>
  This user has <?= $profiled_user->nfollowers ?> followers.
<? } ?>

<br>

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

<div id=streams_container>
  <div class=stream >
    <h2>Following:</h2>
    <ul>
      <? foreach($following as $u ) { ?>
        <li> 
          <a href="javascript:void(0)" onclick="show_user_profile(<?=$u->user_id?>)">
            <?= MyUser::full_name($u) ?></a>
          </a>
        </li>
      <? } ?>
    </ul>
  </div>

  <div class=stream >
    <h2>Followed By:</h2>
    <ul>
      <? foreach($followers as $u ) { ?>
        <li> 
          <a href="javascript:void(0)" onclick="show_user_profile(<?=$u->user_id?>)">
            <?= MyUser::full_name($u) ?></a>
          </a>
        </li>
      <? } ?>
    </ul>
  </div>

</div>

<div id=context_help class=help>
  <span class="title">This is where you can get more information on a user</span>
  <dl>
  <dt> Profile </dt>
  <dd> You can see who the user is following and who's following them. 
       You can also follow or unfollow the user right from their profile page. </dd>
  <dt> Viewing your own profile? Need to change some settings?</dt>
  <dd> Click "Edit Your Settings" to change your own settings such as your email or password. </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>
