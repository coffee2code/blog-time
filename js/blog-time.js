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

			// Update timestamp in one second intervals.
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
