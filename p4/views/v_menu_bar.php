<ul>
<? if($user) { ?>
    <li> <a href="/stories/new_story">Begin a New Story</a> | </li> 
    <li> <a href="/users">Browse Users</a> | </li> 
    <li> <a href="javascript:void(0);" onclick="show_help();">Need Help?</a> </li> 
<? } else { ?>
  <li> <a href="/">Home</a> | </li> 
  <li> <a href="javascript:void(0);" onclick="show_help();">Need Help?</a> </li> 
<? } ?>
</ul>
