<?php
	require '../classes/Smusher.class.php';


	if (!empty($_REQUEST['smush']))
	{
		echo json_encode(Smusher::Smush($_REQUEST['smush']));
		die;
	}

	ob_start('@ob_gzhandler');
	set_time_limit(0);

	function rglob($pattern, $flags = 0, $path = '')
	{
		if (!$path && ($dir = dirname($pattern)) != '.')
		{
			if ($dir == '\\' || $dir == '/') $dir = '';
			return rglob(basename($pattern), $flags, $dir . '/');
		}
		$paths = glob($path . '*', GLOB_ONLYDIR | GLOB_NOSORT);
		$files = glob($path . $pattern, $flags);
		foreach ($paths as $p)
		{
			$files = array_merge($files, rglob($pattern, $flags, $p . '/'));
		}
		return $files;
	}

	$files = rglob('../*.png', GLOB_NOSORT);

/* Restoring ****************************
	Header('Content-Type: text/plain');
	foreach ($files as $file)
	{
		if (!file_exists(str_replace('.unsmushed', '', $file)))
		{
			copy($file, str_replace('.unsmushed', '', $file));
			echo 'Restored "'.$file.'"'."\n";
			flush();
			ob_flush();
		}
	}
	die;
*/
	$smushed = file_exists('./smusher.txt')
		? array_filter(explode("\n", file_get_contents('./smusher.txt')))
		: array();

	$left = array_diff($files, $smushed);

	$current = count($files)-count($left);
	$total = count($files);
?>
<html>
<head>
	<title>yb::smusher</title>
	<link rel="stylesheet" type="text/css" href="smusher.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="smusher.js"></script>
</head>
<?php flush(); ?>
<body>
	<div id="header">
		<div id="options">
			<a href="#" class="playpause" id="start">start</a>
			<a href="#" class="playpause" id="stop" style="display: none;">stop</a>
		</div>

		<p>
			<span id="current"><?=number_format($current,0,',','.')?></span> von <span id="total"><?=number_format($total,0,',','.')?></span> Dateien<br/>
		</p>
		<p>
			Bytes: <span id="saving_bytes">0</span><br/>
		</p>
		<p>
			Prozent: <span id="saving_percent">0%</span><br/>
		</p>
	</div>
	<div id="progress">
		<div id="percent" style="width: <?=round(100*$current/$total, 2)?>%;">
			<?=round(100*$current/$total,2)?>%
		</div>
	</div>
	<table id="log" cellpadding="2" cellspacing="2">
		<colgroup>
			<col width="70%"/>
			<col width="15%"/>
			<col width="15%"/>
		</colgroup>
		<thead>
			<tr>
				<td>Filename</td>
				<td>Before</td>
				<td>After</td>
				<td>%</td>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
<script type="text/javascript">
//<![CDATA[
var files_to_smush = ['<?=implode("','", $left)?>'];
$('body').data('current', <?=$current?>);
$('body').data('total', <?=$total?>);
$('body').data('running', false);
$('body').data('total_old', 0);
$('body').data('total_new', 0);
//]]>
</script>
</body>
</html>