<h1>This is the profile of <?=$user->first_name?></h1>

<h2>Following:</h2>
<ul>
  <? foreach($following as $u ) { ?>
    <li> <?= $u->first_name ?> </li>
  <? } ?>
</ul>

<h2>Followed By:</h2>
<ul>
  <? foreach($followers as $u ) { ?>
    <li> <?= $u->first_name ?> </li>
  <? } ?>
</ul>
