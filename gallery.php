<?php
	$path = isset($_REQUEST['path'])
		? rtrim($_REQUEST['path'], '/').'/'
		: './';
	$files = glob($path.'*.{gif,jpg,jpeg,png}', GLOB_BRACE);
	foreach ($files as $index=>$file)
	{
		$files[$index] = array(
			'filename'=>''.$file,
			'name'=>basename($file),
		);
	}
?>
<html>
<head>
	<base href="http://<?=$_SERVER['HTTP_HOST']?>/"/>
	<title>Index of <?=$path?></title>
	<link rel="stylesheet" href="gallery.css" type="text/css"/>
	<link rel="stylesheet" href="http://dev.youserbase.org/tleilax/assets/js/jquery.ui.1.7/css/smoothness/jquery-ui-1.7.custom.css" type="text/css"/>
</head>
<body>
	<div id="toolbar" class="ui-widget-header">
		<div class="info">
			Selected images: <span id="checked_count">0</span><br/>
		</div>
		<ul>
			<li class="r0"><a id="reset" href="#reset">Reset</a></li>
			<li class="r1"><a id="sprites" href="gallery_sprites.php">Spritify</a></li>
			<li class="r2"><a id="load" href="gallery_actions.php?action=list_css">Load</a></li>
			<li class="r3"><a id="toggle" href="#toggle">Toggle</a> <input type="checkbox" checked="checked"/></li>
		</ul>
	</div>
	<ul id="images">
<?php foreach ($files as $file): ?>
		<li class="ui-corner-all">
			<img src="<?=$file['filename']?>" class="thumbnail" alt="" title="<?=$file['name']?>"/>
			<br/>
			<span><?=$file['name']?></span>
		</li>
<?php endforeach; ?>
	</ul>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/jquery-ui.min.js" type="text/javascript"></script>
	<script src="gallery.js" type="text/javascript"></script>
</body>
</html>
