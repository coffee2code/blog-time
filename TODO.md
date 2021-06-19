# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Add support for per-user setting for controlling if admin toolbar widget should be shown
  * If not shown, don't enqueue JS or CSS
  * Show by default, but add a filter to change default to off by default
* Add support for per-user setting for blog time format shown in admin toolbar widget (uses site default if not customized by user)
  * Applies at least to admin bar widget.
  * Also apply anywhere blog time is shown, such as widget or template tag?
    * If so, then consider adding per-instance widget and template tag config "Allow custom user time formatting?"
    * Or, decisions over options: only use user's time format if widget or template tag uses site default. e.g. Defining a custom time format for a widget would use the widget's time format and not the user's preference in that case.
* Expose REST API endpoint for blog time and use it instead of admin-ajax
* Move away from Moment.js to something like date-fns since Moment has been placed into maintenance mode.
* Add help panel tab with time format tokens so users aren't forced to go to php.net
* Allow blog time format to be configured via constant. If set, then don't show setting. Or do, but have separate constant to disable setting.
* De-emphasize (completely?) the static clock in the plugin's extended description.
* Consider adding setting for other filterable feature: disable_active_clock.
  * If no other settings to be added, then could probably be including alongside the General settings page
  * If other settings added (e.g. hide_admin_bar_widget_by_default), then a dedicated settings page may be warranted
* Add as a block?

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/blog-time/) or on [GitHub](https://github.com/coffee2code/blog-time/) as an issue or PR).