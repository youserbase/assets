$(function() {
	$('body').data('title', $('title').text());

	$('#progress').hide();

	$('body').bind('iddqd', function(e, state) {
		if (state == $('body').data('running')) {
			return;
		}
		$('body').data('running', state);

		$('.playpause').toggle();
		$('#progress:not(:visible)').toggle('slide', {direction: 'up'});
	});
	$(document).keypress(function(e) {
		var key = e.which||e.charCode||e.keyCode;
		if (key!=32 && key!=27) {
			return;
		};
		$('body').trigger('iddqd', (key==27)?false:!$('body').data('running'));
		return false;
	});

	window.setTimeout(function() {
		if (files_to_smush.length==0) {
			$('body').trigger('iddqd', false).unbind('iddqd');
			$('#options').hide('slow');
			return;
		}
		if (!$('body').data('blocked') && $('body').data('running')) {
			work();
		}
		window.setTimeout(arguments.callee, $('body').data('interval'));
	}, $('body').data('interval'));
});

$('.playpause').live('click', function() {
	$('body').trigger('iddqd', !$('body').data('running'));
	return false;
});

$('body').data('interval', 50);

function number_format(number) {
	number = '' + number;
	var result = [];
	while (number.length) {
		result.unshift(number.substr(-3));
		number = number.substring(0, number.length-3);
	}
	return result.join('.');
}

function work() {
	var file = files_to_smush.shift();
	$('body').data('blocked', true);

	$('#log tbody').prepend('<tr class="a'+($('body').data('current')%2)+'"><td>'+file+'</td><td></td><td></td><td><img src="http://assets.youserbase.org/images/ajaxload.gif"/></td></tr>');
	$.getJSON('index.php?smush=' + file, function(json) {
		var percent = json.new/json.old;
		$('#log tbody tr:first td:eq(1)').text(number_format(json.old));
		$('#log tbody tr:first td:eq(2)').text(number_format(json.new));
		$('#log tbody tr:first td:eq(3)').text((100-percent*100).toFixed(2)+'%');
		$('#log tbody tr:first td:eq(3)').animate({backgroundColor: 'rgb('+Math.floor(255*percent)+','+Math.floor(255-255*percent)+',0)'}, 1000);
		$('#log tbody tr:gt(16):not(.dead)').addClass('dead').fadeOut('fast', function() {
			$(this).remove();
		});

		$('body').data('total_old', $('body').data('total_old')+json.old);
		$('body').data('total_new', $('body').data('total_new')+json.new);

		$('body').data('current', $('body').data('current')+1);
		$('#current').text(number_format($('body').data('current')));
		percent = $('body').data('total_new')/$('body').data('total_old');
		$('#percent').css({width: ($('body').data('current')/$('body').data('total')*100).toFixed(2)+'%'}).text(($('body').data('current')/$('body').data('total')*100).toFixed(2)+'%').animate({backgroundColor: 'rgb('+Math.floor(255*percent)+','+Math.floor(255-255*percent)+',0)'}, 1000);
		$('#saving_bytes').text(number_format($('body').data('total_old') - $('body').data('total_new')));
		$('#saving_percent').text((100 - $('body').data('total_new')/$('body').data('total_old')*100).toFixed(2)+'%');

		document.title = $('body').data('title') + ' (' + ($('body').data('current')/$('body').data('total')*100).toFixed(2) + '%)';

		$('body').data('blocked', false);
	});
}