<style type="text/css">
div {
	color: #888;
	display: inline-block;
	font: 11px monospace;
	margin: 2px;
	padding: 2px;
	width: 250px;
}
div:hover {
	background-color: #000;
	border: 1px solid #444;
	color: #fff;
	cursor: help;
	margin: 1px;
}
img {
	margin-right: 5px;
	vertical-align: middle;
}
div input {
	background-color: inherit;
	border: 0;
	color: inherit;
	cursor: help;
	display: none;
	font: 11px monospace;
	margin: 0;
	padding: 0;
}
div:hover span {
	display: none;
}
div:hover input {
	display: inline;
}
</style>
<?php
foreach (glob('*.png') as $image)
{
	printf('<div><img src="%s" alt=""/><span>%s</span><input type="text" value="%s" onfocus="this.select();"/></div>', $image, $image, $image);
}
?>