<form action="gallery_actions.php" method="post">
	<label for="name">Spritenamensraum:</label>
	<input type="text" id="name" name="name"/>
	<br/>

	<label for="prefix">Prefix:</label>
	<input type="text" id="prefix" name="prefix" value="/*[[ASSETS:"/>
	<br/>

	<label for="postfix">Postfix:</label>
	<input type="text" id="postfix" name="postfix" value="]]*/"/>
	<br/>

	<label for="width">Dimension:</label>
	<input type="text" id="width" name="width" class="small"/>
	x
	<input type="text" id="height" name="height" class="small"/>

	<br/>
	<div id="margin-form" style="text-align: center;">

		<input type="text" id="margin_top" name="margin[top]" value="0"/>
		<br/>

		<input type="text" id="margin_left" name="margin[left]" value="0"/>
		Rahmen
		<input type="text" id="margin_right" name="margin[right]" value="0"/>
		<br/>

		<input type="text" id="margin_bottom" name="margin[bottom]" value="0"/>
	</div>

	<input type="hidden" name="action" value="generate_sprites"/>
</form>
