jQuery(function($) {
	$('#toolbar a').click(function() {
		var selection = $('#images li.checked');
		switch ($(this).attr('id')) {
			case 'toggle':
				var hidden = $(this).next(':checkbox:checked').length>0;
				$(this).next(':checkbox').attr('checked', !hidden);
				$('#images').toggleClass('hide-names', hidden);
				console.log(hidden, $('#images'));
				break;
			case 'reset':
				selection.removeClass('checked');
				break;
			case 'load':
				$('<div/>').load( $(this).attr('href'), function(response) {
					if (!response) {
						return;
					}
					$(this).dialog({
						title : 'Sprites laden',
						modal : true,
						buttons : {
							'Ok' : function() {
								$.get('gallery_actions.php?action=load_css', {css:$('select',this).val()}, function(ids) {
									$('#images li img').each(function() {
										var id = this.src.replace(/\.[^.]+$/, '').split('/').slice(-1)[0],
											self = $(this);
										$.each(ids, function() {
											if (this==id) {
												self.closest('li').addClass('checked');
											}
										});
									});
									console.log(ids);
								}, 'json');
								$(this).dialog('close');
							},
							'Cancel' : function() {
								$(this).dialog('close');
							}
						}
					});
				});
				break;
			case 'sprites':
				if (!selection.length) {
					return false;
				}
				$('<div/>').load( $(this).attr('href'), function() {
					var width = 0,
						height = 0,
						name = '',
						self = $(this);
					$('img', selection).each(function() {
						name = name||this.src.replace(/^http:\/\/[^\/]+\//, '').replace(/\.[^.]+$/, '').split('/').slice(0, -1).join('-');
						width = Math.max(width, this.naturalWidth||this.width);
						height = Math.max(height, this.naturalHeight||this.height);
						$('form', self).append('<input type="hidden" name="files[]" value="'+this.src+'"/>');
					});
					$('#width', this).val(width);
					$('#height', this).val(height);
					$('#name', this).val(name);

					$(this).dialog({
						title : 'Sprites generieren',
						modal : true,
						width: 320,
						buttons : {
							'Ok' : function() {
								$(this).dialog('close');
								var result = $('<div>Berechne Sprites</div>').dialog({title:'Status',modal:true});
								$.post( $('form', this).attr('action'), $('form', this).serializeArray(), function(response) {
									result.dialog('close');
									$('<div/>').html(response).dialog({
										title : 'Ergebnis',
									});
								});
							},
							'Cancel' : function() {
								$(this).dialog('close');
							}
						}
					});
				});
				break;
			default:
				console.log(this);
				break;
		}
		return false;
	});

	$('img.thumbnail').live('dblclick', function() {
		if (this.width!=this.naturalWidth || this.height!=this.naturalHeight) {
			window.open(this.src);
		}
	});
	$('#images li').live('click', function() {
		$(this).toggleClass('checked');
		$('#checked_count').text( $('#images li.checked').length );
	});
});