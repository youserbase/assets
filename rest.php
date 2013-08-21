<?php
	Header('Content-Type: text/plain');
	if (false and empty($_POST))
	{
		Header('Status: 401');
		die('Illegal call');
	}
	$path = array_filter(explode('/', $_SERVER['PATH_INFO']));
	var_dump($path);
?>