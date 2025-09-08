<?php
// phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Images are part of the plugin, not Media Library.
?>

<div class="r34ics-gradient-bg postbox"><div class="inside">

	<a href="https://icscalendar.com/" target="_blank"><img src="<?php echo esc_url(plugins_url('assets/ics-calendar-logo-2023.svg', dirname(dirname(__FILE__)))); ?>" alt="ICS Calendar" style="display: block; height: auto; margin: 0 auto 1.5em auto; width: 180px;" width="180" height="62" /></a>
	
	<h3><?php esc_html_e('User Guide', 'ics-calendar'); ?></h3>
	
	<p><?php esc_html_e('Our complete user guide is available with full translation into dozens of languages on our website:', 'ics-calendar'); ?><br />
	<strong><a href="https://icscalendar.com/user-guide/">icscalendar.com/user-guide</a></strong></p>
	
	<h3><?php esc_html_e('Support', 'ics-calendar'); ?></h3>
	
	<p><?php
	/* translators: 1. HTML tag 2. HTML tag */
	printf(esc_html__('For assistance, please use our %1$ssupport request form%2$s.', 'ics-calendar'), '<strong><a href="https://icscalendar.com/support/" target="_blank" style="white-space: nowrap;">', '</a></strong>');
	?></p>
	
	<?php
	// Restrict System Report to admins / super admins
	if	(
				(is_multisite() && current_user_can('setup_network')) ||
				(!is_multisite() && current_user_can('manage_options'))
			)
	{
		?>
		<p><?php
		/* translators: 1. HTML tags 2. HTML tags */
		printf(esc_html__('When contacting support, please include the %1$sSystem Report%2$s from this page.', 'ics-calendar'), '<strong><a href="#system-report" style="white-space: nowrap;">', '</a></strong>');
		?></p>
		<?php
	}
	?>

	<h3><?php esc_html_e('Spread the word!', 'ics-calendar'); ?></h3>
	
	<p><?php
	/* translators: 1: Plugin title (do not translate) 2: HTML tag 3: HTML tag */
	printf(esc_html__('If you find %1$s useful, you can help support our continued growth and development with a %2$sfive-star review%3$s. Thank you!', 'ics-calendar'), '<strong>ICS Calendar</strong>', '<strong><a href="https://wordpress.org/support/plugin/ics-calendar/reviews/#new-post" target="_blank" style="white-space: nowrap;">', '</a></strong>');
	?></p>

</div></div>

<div class="postbox"><div class="inside">

	<a href="https://room34.com/" target="_blank"><img src="<?php echo esc_url(plugins_url('assets/room34-logo-on-white.svg', dirname(dirname(__FILE__)))); ?>" alt="Room 34 Creative Services" style="display: block; height: auto; margin: 1.5em auto; width: 180px;" width="180" height="55" /></a> 
			
	<p style="text-align: center;">ICS Calendar v. <?php echo esc_html(get_option('r34ics_version')); ?></p>

</div></div>

<?php
// phpcs:enable
