
<h1>This is the profile of <?= MyUser::full_name($profiled_user) ?>
<? if($viewing_self) { ?> <span class="notice">(That's you!)</span> <? } ?>
</h1>

<? if($viewing_self) { ?>
  <h3>These are your current settings</h3>
  Share your content with other users: <?= $user->publish_content ? "yes" : "no" ?><br>
  Incorporate content from other users: <?= $user->use_external_content ? "yes" : "no"  ?><br>
  <h3><a href="/users/edit">Edit Your Settings</a></h3>
<? } ?> 

<br>

<? if(!$viewing_self) { ?>
  <? $puid = $profiled_user->user_id ?>
<? } ?>

<? if($user->superuser == 1) { ?>
  <h3> User Images </h3>
  <ul class=imagelist>
  <? foreach(MyUser::images_for($profiled_user->user_id) as $img) { ?>
    <li class=character > <img src="<?= "/".$img->filename ?>"> </li>   
  <? } ?>
  </ul>

  <br>

  <h3> Responses</h3>
  <ul class=imagelist>
  <? foreach(MyUser::responses_for($profiled_user->user_id) as $resp) { ?>
    <li class=character > <div><img src="<?= "/".$resp->image_filename?>"> <?= $resp->text ?> </div></li>   
  <? } ?>
  </ul>

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
