# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Add support for per-user setting for controlling admin toolbar widget (and if not shown, don't enqueue JS or CSS)
* Allow user to specify Moment.js-style time format string. (Perhaps by prepending "momentjs:" to it.)
* Expose REST API endpoint for blog time and use it instead of admin-ajax
* Move away from Moment.js to something like date-fns since Moment has been placed into maintenance mode.
* Add help panel tab with time format tokens so users aren't forced to go to php.net
* Remove long-deprecated `'blog_time_format'` filter

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/blog-time/) or on [GitHub](https://github.com/coffee2code/blog-time/) as an issue or PR).