function stripHtml(text)
{
     var regexp = /<("[^"]*"|'[^']*'|[^'">])*>/gi;
     return text.replace(regexp, '');
}
function tumblr_ready(data)
{
	var $ = jQuery;
	var container = $('#tumblr_container').removeClass('loading');
	for (var i in data.posts)
	{
		var p = data.posts[i];
		var _item = $('<a></a>').addClass('tumblr_item').addClass('tumblr_'+p['type']);
		_item.attr('href', p['url-with-slug']).attr('rel', 'external');
		
		var _what = $('<div/>');
		var _title = $('<span/>');
		switch(p['type']) {
			case 'photo':
				_what = $('<img/>');
				_what.attr('src', p['photo-url-400']).attr('alt', stripHtml(p['photo-caption']));
				_what.load(function() {
					if ($(this).height() > $(this).width()) 
						$(this).addClass('ratio_w');
					else
						$(this).addClass('ratio_h');
				});

				_title = _title.html(p['photo-caption']).text();
				break;
			case 'video':
				_what.html(p['video-source']);
				_title = _title.html(p['video-caption']).text();
				break;
			case 'quote':
				_what.html('&#8221;' + p['quote-text'] + '&#8220;');
				_title = _title.html(p['quote-text']).text();
				break;
			case 'regular':
				_title = _title.html(p['regular-title']).text();
				_what.text(_title);
				
				var _excerpt = $('<span/>').html(stripHtml(p['regular-body'])).text().substr(0,400);
				if (_excerpt.length)
				{
					_excerpt+= '...';
					if (_title.length)
						_what.append($('<span/>').text(_excerpt));
					else
						_what.text(_excerpt);
				}
				
				break;
			case 'link':
				_what.text(p['link-text']);
				_title = _title.html(p['link-text']).text();
				
				if (p['link-description'].length)
					_what.append($('<span/>').text(stripHtml(p['link-description'])));
				break;
			case 'conversation':
				_title = _title.html(p['conversation-title']).text();
				for (var i in p['conversation-lines'])
				{
					var _c = p['conversation-lines'][i];
					var _row = $('<div/>').addClass(i%2 ? 'o' : '');
					$('<span/>').addClass('l').html(_c['label'] + '&lrm; ').appendTo(_row);
					$('<span/>').text(_c['phrase']).appendTo(_row);
					_what.append(_row);
				}
				break;
			case 'audio':
				if (wp_tumblr_audio_color)
					var _embed = p['audio-player'].replace(/color=[0-9a-fA-F]{6}/, 'color=<?php echo $audio_color ?>');
				else
					var _embed = p['audio-player'];
				_what.html(_embed + p['audio-caption']);
				_title = $(p['audio-caption']).text();
				break;
		}
		
		// append the thing
		_what.addClass('what');
		_item.append(_what).attr('title', _title);
		
		// RTL class for everything that contains Hebrew
		if (_what.text().match(/[א-ת]+/))
			_item.addClass('rtl');

		// hover effect for textual posts (videos & audios are rendered objects)
		if (p['type'] != 'video' && 'audio' != p['type'])
		{
			_item.hover(
				function() {
					var _t = $('<div></div>').addClass('hover');
					_t.text($(this).attr('title'));
					$(this).append(_t);
					if ($(this).hasClass('tumblr_photo'))
						$(this).find('img.what').addClass('zoom');
				},
				function() {
					$(this).find('.hover').remove();
					if ($(this).hasClass('tumblr_photo'))
						$(this).find('img.what').removeClass('zoom');
				}
			);
		}
		// audio & video posts aren't clickable
		else
		{
			_item.attr('disabled', 'disabled');
		}
		
		// that's an item.
		container.append(_item);
	}
}

