if (jQuery) {
	jQuery(document).ready(function($) {

		var c2c_blog_time_update_clock = function(c) {
			// If time format is not embedded in markup (as the case for a widget), then
			// use configured value.
			var embedded_time_format = $(c).data('time-format');
			var time_format = embedded_time_format ? embedded_time_format : c2c_BlogTime.time_format;
			var utc_offset = c2c_BlogTime.utc_offset;
			var now = moment().utcOffset(utc_offset).format(time_format);
			$(c).find('.c2c-blog-time-widget-display').html("<span class='clocktime'>"+now+"</span>"); //+":"+s

			// Determine update interval.
			if ( time_format.indexOf('s') > -1 ) {
				// Seconds are being displayed, so must update every second.
				interval_seconds = 1;
			} else if ( time_format.indexOf('m') > -1 ) {
				// Minutes are being displayed, so update every 10 seconds.
				interval_seconds = 10;
			} else {
				// Any longer time period is fine with an update once a minute.
				// While the interval could be made even longer, when the page is
				// loaded on the cusp of a time/date change, we don't want the
				// time display to be wrong for too long.
				interval_seconds = 60;
			}
			// Update timestamp in the determined intervals.
			setTimeout(function() { c2c_blog_time_update_clock( $(c) ) }, interval_seconds * 1000);
		}

		$.each($('.c2c-blog-time-widget'), function(){
			var dynamic_clock = $(this).find('.c2c-blog-time-dynamic');
			var display = $(this).find('.c2c-blog-time-widget-display');
			if (dynamic_clock.length > 0) { // If c2c-blog-time-dynamic is present, clock should be dynamic
				// Set a CSS class for container to denote dynamic clock.
				$(this).parent().addClass('c2c-blog-time-dynamic');
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
