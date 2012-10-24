<ul>
<? if($user) { ?>
    <li> <a href="/streams">Home</a> | </li> 
    <li> <a href="/streams/manage">Manage Streams</a> | </li> 
    <li> <a href="/posts">My Posts</a> | </li> 
    <li> <a href="/users">Browse Users</a> </li> 
<? } else { ?>
  <li> <a href="/">Home</a> </li> 
<? } ?>
</ul>
