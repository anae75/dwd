<!DOCTYPE html>
<html>
<head>
	<title><?=@$title; ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
	
	<!-- JS -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>

        <link rel="Stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
				
	<!-- Controller Specific JS/CSS -->
	<?php echo @$client_files; ?>
	
</head>

<body>	

  <div id=outer_container>

    <div class=menubar>
      <div class=leftmenu>
        <?= View::instance('v_menu_bar'); ?>
      </div>
        <a href="/"><img src="/logo-sm.png" title="Welcome to MMMMicroblogger"></a>
      <div class=rightmenu>
        <?= View::instance('v_login_bar'); ?>
      </div>
    </div>

    <? if(Flash::has_message()) { ?>
    <div class="notice">
      <?= Flash::get(); ?>
    </div>
    <? } ?>

    <div id=content_container class=maincontent>
	<?=$content;?> 
    </div>

  </div>

</body>
</html>
