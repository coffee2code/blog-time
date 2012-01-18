if (jQuery) {
	jQuery(document).ready(function($) {

		var c2c_addleadingzero = function(i){
			return ( i < 10 ) ? ('0' + i) : i;
		},
		c2c_blog_time_update_clock = function(c) {
			today = new Date();

			// Existing time diff from local time may have been previously stored.
			diff = $(c).data('c2c-server-time-diff');
			if ( diff == undefined ) { // If not stored, ascertain it
				raw_servertime = parseFloat($(c).find('.c2c-blog-time-widget-time').text()) * 1000;
				servertime = new Date(raw_servertime + 5000); // Add 5 seconds to better sync w/ local time
				// Store time diff
				diff = $(c).data('c2c-server-time-diff', raw_servertime - today.getTime());
			} else {
				servertime = new Date(today.getTime() + diff);
			}

			var h  = servertime.getHours(),
			    m  = servertime.getMinutes(),
				s  = servertime.getSeconds(),
				ap = " AM";
			if ( h > 11 ) ap = " PM";

			// add a zero in front of numbers 0-9
			h = c2c_addleadingzero(h);
			m = c2c_addleadingzero(m);
			s = c2c_addleadingzero(s);

			// Use non-military time
			if ( h > 12 )
				h -= 12;
			else if ( h == 0 )
				h = 12;

			$(c).find('.c2c-blog-time-widget-display').html("<span class='clocktime'>"+h+":"+m+ap+"</span>"); //+":"+s
			setTimeout(function() { c2c_blog_time_update_clock( $(c) ) }, 1000);
		}

		$.each($('.c2c-blog-time-widget'), function(){
			var dynamic_clock = $(this).find('.c2c-blog-time-dynamic');
			var display = $(this).find('.c2c-blog-time-widget-display');
			if (dynamic_clock.length > 0) { // If c2c-blog-time-dynamic is present, clock should be dynamic
				display.find('a')
					.attr('title', '')
					.click(function() { return false; });
					c2c_blog_time_update_clock($(this));
			}
			else {
				display.click(function() {
					$.get(ajaxurl, {action: 'report_time'}, function(data) {
						display.find('a').html(data);
					});
					return false;
				});
			}
		});
	});
}
