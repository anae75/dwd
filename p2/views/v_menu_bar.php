<ul>
<? if($user) { ?>
    <li> <a href="/streams">Home</a> | </li> 
    <li> <a href="/streams/manage">Manage Streams</a> | </li> 
    <li> <a href="/posts">My Posts</a> | </li> 
    <li> <a href="/users">Browse Users</a> | </li> 
    <li> <a href="javascript:void(0);" onclick="show_help();">Need Help?</a> </li> 
<? } else { ?>
  <li> <a href="/">Home</a> | </li> 
  <li> <a href="javascript:void(0);" onclick="show_help();">Need Help?</a> </li> 
<? } ?>
</ul>
