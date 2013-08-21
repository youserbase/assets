<?php
switch (@$_REQUEST['action'])
{
	case 'list_css':
		if (!count($files=glob('sprites/*.css')))
		{
			die;
		}
		echo '<select>';
		foreach ($files as $css)
		{
			echo '<option>'.basename($css, '.css').'</option>';
		}
		echo '</select>';
		break;
	case 'load_css':
		$content = file_get_contents('sprites/'.$_GET['css'].'.css');
		$matched = preg_match_all('/-sprite\.([^\s.]+)/', $content, $matches, PREG_SET_ORDER);

		$files = array();
		foreach ($matches as $match)
		{
			if ($match[1]!='icon')
			{
				array_push($files, $match[1]);
			}
		}

		echo json_encode(array_merge(array_unique($files)));
		break;
	case 'generate_sprites':
		$width = $_POST['margin']['left']+$_POST['width']+$_POST['margin']['right'];
		$height = $_POST['margin']['top']+$_POST['height']+$_POST['margin']['bottom'];
		$size = count($_POST['files']);

		$filename = 'sprites/'.$_POST['name'];

		$offset = 0;
		$front_padding = 2*$_POST['margin']['left'] + $_POST['width'];
		$css  = <<<CSS
.{$_POST['name']}-sprite {
	background-image: url({$_POST['prefix']}{$filename}{$_POST['postfix']}.png);
	background-position: {$width}px {$height}px;
	background-repeat: no-repeat;
}
.{$_POST['name']}-sprite.front {
	padding-left: {$front_padding}px;
}
.{$_POST['name']}-sprite.icon {
	display: inline-block;
	*dislay: inline;
	height: {$_POST['height']}px;
	width: {$_POST['width']}px;
	zoom: 1;
}
CSS;
		$canvas = imagecreatetruecolor($width, $size*$height);
		imagealphablending($canvas, false);
		$bg = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
		imagefill($canvas, 0, 0, $bg);
		imagesavealpha($canvas, true);
		imagealphablending($canvas, true);
		foreach ($_POST['files'] as $index=>$file)
		{
			$id = preg_replace('/[^a-z]/', '-', preg_replace('/\.[^.]+$/', '', strtolower(basename($file))));
			$css .= "\n".<<<CSS
.{$_POST['name']}-sprite.{$id} {
	background-position: 0 -{$offset}px;
}
CSS;
			if ($_POST['margin']['top']>0 or $_POST['margin']['left']>0)
			{
				$clean_offset = $offset + $_POST['margin']['top'];
				$css .= "\n".<<<CSS
.{$_POST['name']}-sprite.{$id}.clean, .{$_POST['name']}-sprite.{$id}.icon  {
	background-position: {$_POST['margin']['left']}px -{$clean_offset}px;
}
CSS;
			}

			$file = preg_replace('/^http:\/\/[^\/]+\//', '', $file);
			$tile = imagecreatefromstring(file_get_contents($file));
			imagecopy($canvas, $tile, 0+$_POST['margin']['left'], $offset+$_POST['margin']['top'], 0, 0, $_POST['width'], $_POST['height']);
			imagedestroy($tile);

			$offset += $height;
		}

		imagepng($canvas, './'.$filename.'.png', 9);
		imagedestroy($canvas);

		file_put_contents('./'.$filename.'.css', $css);

		printf('%u * %ux%u [%u x %u]<br/>', $size, $width, $height, $width, $size*$height);
		printf('Filesize: '.number_format(filesize('./'.$filename.'.png'))).' byte<br/>';

		break;
	default:
		echo 'Unknown action';
		break;
}
?>