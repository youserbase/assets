<?php
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

	define('MAX', 256);
	set_time_limit(0);

	$files = rglob('*.png', GLOB_NOSORT);
	$smushed = file_exists('./smusher.txt')
		? array_filter(explode("\n", file_get_contents('./smusher.txt')))
		: array();

	$left = array_diff($files, $smushed);
	$work_package = array_slice($left, 0, MAX);

	printf("Found %u files, %u smushed (%s%%)<br/>", count($files), count($smushed), number_format(count($smushed)/count($files)*100, 2, ',', '.'));

	$total_old = $total_new = 0;

	foreach ($work_package as $file)
	{
		if (!file_exists('/tmp/smusher'))
		{
			mkdir('/tmp/smusher');
		}
		$temp_file = '/tmp/smusher/'.md5(uniqid($file, true));

		$output = exec('/usr/local/sbin/pngcrush -brute -rem alla -fix -reduce '.$file.' '.$temp_file);

		$old_size = filesize($file);
		$new_size = filesize($temp_file);

		$total_old += $old_size;
		if ($new_size < $old_size)
		{
			$total_new += $new_size;

			echo "+ {$file} reduced to ".number_format(100-$new_size/$old_size*100,2,',','.')."%<br/>";

			rename($file, $file.'.unsmushed');
			copy($temp_file, $file);
		}
		else
		{
			$total_new += $old_size;
			echo "- {$file} could not be reduced<br/>";
		}
		echo '<script>window.scrollTo(0,999999);</script>';
		flush();

		unlink($temp_file);

		array_push($smushed, $file);
	}
	printf("Gained %s byte (%s%%) by smushing", number_format($total_old - $total_new), number_format(100-$total_new/$total_old*100, 2, ',', '.'));

	file_put_contents('./smusher.txt', implode("\n", $smushed));
?>