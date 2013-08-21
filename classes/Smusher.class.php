<?php
class Smusher
{
	public static function Smush($filename)
	{
		if (!file_exists('/tmp/smusher'))
		{
			mkdir('/tmp/smusher');
		}
		$temp_file = '/tmp/smusher/'.md5(uniqid($filename, true)).'.png';

		$output = exec('/usr/local/sbin/pngcrush -brute -rem alla -fix -reduce '.$filename.' '.$temp_file);
//		$output = exec('/usr/local/sbin/pngout '.$filename.' '.$temp_file);

		$old_size = filesize($filename);
		$new_size = filesize($temp_file);

		if ($new_size < $old_size)
		{
			@rename($filename, $filename.'.unsmushed');
			@copy($temp_file, $filename);
		}

		$fp = fopen('./smusher.txt', 'a+');
		fputs($fp, $filename."\n");
		fclose($fp);

		unlink($temp_file);

		return array(
			'old' => $old_size,
			'new' => $new_size,
		);
	}
}
?>