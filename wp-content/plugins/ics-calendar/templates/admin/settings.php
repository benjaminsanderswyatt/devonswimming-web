<h3><?php esc_html_e('Appearance', 'ics-calendar'); ?></h3>

<p class="r34ics-input">
	<label for="r34ics_colors_match_theme_json"><input type="checkbox" name="colors_match_theme_json" id="r34ics_colors_match_theme_json"<?php if (get_option('r34ics_colors_match_theme_json')) { echo ' checked="checked"'; } ?> /> <strong><?php esc_html_e('Match calendar color palette to current Block Theme colors', 'ics-calendar'); ?></strong><small class="beta-indicator"><?php esc_html_e('beta', 'ics-calendar'); ?></small></label>
	<span class="description"><small class="r34ics-help"><span class="help_content">
	<?php
	/* translators: 1: Plugin name (do not translate) 2: HTML tag and filename 3: HTML tag and filename */
	printf(esc_html__('Replaces the %1$s default neutral color palette with nearest matches from the current Site Editor Styles color palette or the %2$s file in your theme. Color selections are algorithmic, and some combinations may not pass accessibility contrast tests. This setting has no effect if you are using a "classic" theme that does not include a %3$s file.', 'ics-calendar'), 'ICS Calendar', '<code>theme.json</code>', '<code>theme.json</code>');
	?>
	<br /><br />
	<?php
	/* translators: 1: HTML tag 2: HTML tag */
	printf(esc_html__('%1$sThis is currently a beta feature.%2$s It may require making some adjustments to your calendar settings or CSS for optimal display, especially if you have color-coded your feeds. Functionality is subject to change.', 'ics-calendar'), '<strong style="color: crimson;">', '</strong>');
	?>
	</span></small></span>
</p>

<p class="r34ics-input">
	<label for="r34ics_colors_darkmode"><input type="checkbox" name="colors_darkmode" id="r34ics_colors_darkmode"<?php if (get_option('r34ics_colors_darkmode')) { echo ' checked="checked"'; } ?> /> <strong><?php esc_html_e('Dark mode', 'ics-calendar'); ?></strong><small class="beta-indicator"><?php esc_html_e('beta', 'ics-calendar'); ?></small></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php
	/* translators: 1: HTML tags 2: HTML tag 3: HTML tag and code 4: HTML tag and code */
	printf(esc_html__('Inverts color palette (light colors on a dark background). %1$sNote:%2$s Dark mode also automatically applies style changes similar to the %3$s and %4$s shortcode parameters, so you do not need to include them in your shortcode.', 'ics-calendar'), '<br /><br /><strong>', '</strong>', '<code>solidcolors</code>', '<code>whitetext</code>');
	?>
	<br /><br />
	<?php
	/* translators: 1: HTML tag 2: HTML tag */
	printf(esc_html__('%1$sThis is currently a beta feature.%2$s It may require making some adjustments to your calendar settings or CSS for optimal display, especially if you have color-coded your feeds. Functionality is subject to change.', 'ics-calendar'), '<strong style="color: crimson;">', '</strong>');
	?>
	</span></small></span>
</p>

<?php do_action('r34ics_settings_fields_appearance'); ?>

<h3><?php esc_html_e('Loading', 'ics-calendar'); ?></h3>

<p class="r34ics-input">
	<label for="r34ics_ajax_by_default"><input type="checkbox" name="ajax_by_default" id="r34ics_ajax_by_default"<?php if (get_option('r34ics_ajax_by_default')) { echo ' checked="checked"'; } ?> /> <strong><?php esc_html_e('Always use AJAX to render calendars (recommended)', 'ics-calendar'); ?></strong></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php esc_html_e('When checked, all calendars will render via AJAX (asynchronously after the page loads). This is especially useful in conjunction with other caching plugins, or if your calendars contain a large number of events. Normally, calendar data is sent directly in the HTML of the initial page load unless AJAX has been specified in the shortcode.', 'ics-calendar'); ?></span></small></span>
</p>

<p class="r34ics-input">
	<label for="r34ics_display_calendar_memory_limit"><strong><?php esc_html_e('Increase memory limit when rendering calendars', 'ics-calendar'); ?>:</strong>
	<?php
	echo wp_kses(
		r34ics_memory_limit_select(
			'display_calendar_memory_limit',
			'r34ics_display_calendar_memory_limit',
			(intval(get_option('r34ics_display_calendar_memory_limit')
				? get_option('r34ics_display_calendar_memory_limit')
				: r34ics_memory_limit_mb())
			)
		),
		r34ics_select_allowed()
	);
	?>
	</label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php
	/* translators: 1: HTML tag */
	printf(esc_html__('If your calendar is failing to load with an "out of memory" fatal error, try increasing the memory limit. A minimum of 512 MB is recommended. %1$s Notes: 1) Some hosting providers may not allow applications to override the server default setting. 2) It is not possible to set this option lower than the current default memory limit on your server.', 'ics-calendar'), '<br /><br />');
	?></span></small></span>
</p>

<p class="r34ics-input">
	<label for="r34ics_url_get_contents_legacy_method"><input type="checkbox" name="url_get_contents_legacy_method" id="r34ics_url_get_contents_legacy_method"<?php if (get_option('r34ics_url_get_contents_legacy_method')) { echo ' checked="checked"'; } ?> /> <strong><?php esc_html_e('Use legacy feed request method', 'ics-calendar'); ?></strong></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php esc_html_e('If your site has stopped retrieving feeds after the version 11 update, turn on this option to use the legacy request method.', 'ics-calendar'); ?></span></small></span>
</p>

<p class="r34ics-input">
	<label for="r34ics_default_shortcode"><strong><?php esc_html_e('Default shortcode', 'ics-calendar'); ?>:</strong><br />
	<textarea name="default_shortcode" id="r34ics_default_shortcode" style="max-width: 400px; width: 100%; height: 4.5rem;" /><?php echo esc_attr(get_option('r34ics_default_shortcode')); ?></textarea></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php
	/* translators: 1: HTML tag and sample code */
	printf(esc_html__('If your site will include a large number of similarly configured calendar shortcodes, you can enter a default "base" shortcode here with the parameters that should be the same for all of the calendars. Individual shortcodes on the site can then include only the parameters that are unique to that calendar, as such: %1$s Any parameters included in the individual shortcode will override what is set here.', 'ics-calendar'), '<code>[ics_calendar url="&hellip;"]</code><br /><br />');
	?></span></small></span>
</p>

<p class="r34ics-input">
	<label for="r34ics_allowed_hosts"><strong><?php esc_html_e('Allow access to these hostnames that resolve to reserved IP addresses', 'ics-calendar'); ?>:</strong><br />
	<textarea name="allowed_hosts" id="r34ics_allowed_hosts" style="max-width: 400px; width: 100%; height: 4.5rem;" /><?php echo esc_attr(implode("\n", get_option('r34ics_allowed_hosts') ? get_option('r34ics_allowed_hosts') : array())); ?></textarea></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php
	/* translators: 1: HTML tag 2: HTML tag 3: HTML tag 4: Plugin name (do not translate) 5: HTML tag */
	printf(esc_html__('If your calendar server is hosted on the same physical server as your website, is part of the same local network, or otherwise uses a %1$sreserved IP address%2$s, enter its hostname here to allow access. For multiple calendar servers, enter one hostname per line. See %3$sthe %4$s documentation%5$s for more information.', 'ics-calendar'), '<a href="https://en.wikipedia.org/wiki/Internet_Protocol_version_4#Special-use_addresses" target="_blank">', '</a>', '<a href="https://icscalendar.com/developer/#http_request_host_is_external" target="_blank">', 'ICS Calendar', '</a>');
	?></span></small></span><br />
	<small><?php
	/* translators: 1: HTML tag 2: HTML tag 3: HTML tag */
	printf(esc_html__('Enter one hostname per line. Hostnames are the base domain name, not a full URL. %1$s For example, you would enter %2$s, not %3$s.', 'ics-calendar'), '<br />', '<code style="font-size: 100%;">example.com</code>', '<code style="font-size: 100%;">https://example.com/path/</code>');
	?></small>
</p>

<?php do_action('r34ics_settings_fields_loading'); ?>

<h3><?php esc_html_e('Caching', 'ics-calendar'); ?></h3>

<p class="r34ics-input">
	<label for="r34ics_transients_expiration"><strong><?php esc_html_e('Transient (cache) expiration', 'ics-calendar'); ?>:</strong> <input type="number" name="transients_expiration" id="r34ics_transients_expiration" value="<?php echo esc_attr(get_option('r34ics_transients_expiration') ? get_option('r34ics_transients_expiration') : 3600); ?>" min="0" max="86400" style="width: 100px;" /> <?php esc_html_e('seconds', 'ics-calendar'); ?></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php esc_html_e('Sets how long calendar feed data should be cached on the server (WordPress transients) before reloading. Default is 3600 (1 hour).', 'ics-calendar'); ?></span></small></span>
</p>

<p class="r34ics-input">
	<label for="r34ics_feed_urls_permanent"><input type="checkbox" name="feed_urls_permanent" id="r34ics_feed_urls_permanent"<?php if (get_option('r34ics_feed_urls_permanent')) { echo ' checked="checked"'; } ?> /> <strong><?php esc_html_e('Permanent feed URL mask IDs', 'ics-calendar'); ?></strong></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php esc_html_e('The AJAX loading method generates masked feed URL IDs, to avoid sending source feed URLs directly in the page HTML. These are normally recycled whenever saving settings or running the "Clear Cached Calendar Data" utility. This can cause problems with certain external caching systems (e.g. Cloudflare), which may continue to request expired IDs. Check this box to permanently retain the masked feed URL IDs.', 'ics-calendar'); ?></span></small></span>
</p>

<?php do_action('r34ics_settings_fields_caching'); ?>

<h3><?php esc_html_e('Miscellaneous', 'ics-calendar'); ?></h3>

<p class="r34ics-input">
	<label for="r34ics_display_add_calendar_button_false"><input type="checkbox" name="display_add_calendar_button_false" id="r34ics_display_add_calendar_button_false"<?php if (get_option('r34ics_display_add_calendar_button_false')) { echo ' checked="checked"'; } ?> /> <strong><?php esc_html_e('Remove "Add ICS Calendar" button in Classic Editor', 'ics-calendar'); ?></strong></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php esc_html_e('By default, ICS Calendar adds an "Add ICS Calendar" button to the toolbar above the WYSIWYG editor when using Classic Editor. Check this box to remove the button. Has no effect on sites using the Block Editor (Gutenberg).', 'ics-calendar'); ?></span></small></span>
</p>

<p class="r34ics-input">
	<label for="r34ics_use_new_defaults_10_6"><input type="checkbox" name="use_new_defaults_10_6" id="r34ics_use_new_defaults_10_6"<?php if (get_option('r34ics_use_new_defaults_10_6')) { echo ' checked="checked"'; } ?> /> <strong><?php esc_html_e('Use new parameter defaults (v.10.6)', 'ics-calendar'); ?></strong></label>
	<span class="description"><small class="r34ics-help"><span class="help_content"><?php
	/* translators: 1: Plugin name (do not translate) 2: HTML tag 3: HTML tag */
	printf(esc_html__('%1$s version 10.6 introduced new default values for several shortcode parameters. New installations automatically use the new default values, but upgraded installations will continue to use the old default values, unless this box is checked. Read more about these changes on %2$sour blog%3$s.', 'ics-calendar'), 'ICS Calendar', '<a href="https://icscalendar.com/updated-parameter-defaults-in-ics-calendar-10-6/" target="_blank">', '</a>');
	?></span></small></span>
</p>

<?php do_action('r34ics_settings_fields_miscellaneous'); ?>
