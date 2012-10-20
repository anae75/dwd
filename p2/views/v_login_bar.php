<? if($user) { ?>
  <ul>
    <li> Welcome, <?= $user->first_name ?> | </li>
    <li> <a href="/users/profile/<?= $user->user_id ?>">My Profile</a> | </li> 
    <li> <a href="/users/logout">Log Out</a> </li> 
  </ul>
<? } else { ?>
  <ul>
    <li> Hello there! <a href="/users/login">Log In</a> or <a href="/users/signup">Sign Up</a> </li>
  </ul>
<? } ?>
