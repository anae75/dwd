<!DOCTYPE html>
<html>
<head>
	<title><?=@$title; ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
	
	<!-- JS -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
				
	<!-- Controller Specific JS/CSS -->
	<?php echo @$client_files; ?>
	
</head>

<body>	

  <div id=outer_container>

    <div class=menubar>
      <span class=leftmenu>
        <?= View::instance('v_menu_bar'); ?>
      </span>
      <span class=rightmenu>
        <?= View::instance('v_login_bar'); ?>
      </span>
    </div>

    <div id=content_container class=maincontent>
	<?=$content;?> 
    </div>

  </div>

</body>
</html>
