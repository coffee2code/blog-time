# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Add support for per-user setting for controlling if admin toolbar widget should be shown
  * If not shown, don't enqueue JS or CSS
  * Show by default, but add a filter to change default to off by default
* Add support for per-user setting for blog time format shown in admin toolbar widget (uses site default if not customized by user)
* Expose REST API endpoint for blog time and use it instead of admin-ajax
* Move away from Moment.js to something like date-fns since Moment has been placed into maintenance mode.
* Add help panel tab with time format tokens so users aren't forced to go to php.net
* Allow blog time format to be configured via constant. If set, then don't show setting. Or do, but have separate constant to disable setting.

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/blog-time/) or on [GitHub](https://github.com/coffee2code/blog-time/) as an issue or PR).